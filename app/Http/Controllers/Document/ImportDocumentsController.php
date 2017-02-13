<?php namespace KlinkDMS\Http\Controllers\Document;

use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Import;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Validation\Validator;
use KlinkDMS\File;
use Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use KlinkDMS\Commands\ImportCommand;
use Illuminate\Support\Facades\Log;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use KlinkDMS\Exceptions\ForbiddenException;
use Illuminate\Http\JsonResponse;
use KlinkDMS\Http\Requests\ImportUpdateRequest;
use Exception;

class ImportDocumentsController extends Controller {

	// USER + DESCR ID (INST + LOCAL DOC ID)
	use DispatchesJobs;
	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $service = null;
        private $user;
        private $childrenArray;

	private $documentsService = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsAdapter\Contracts\KlinkAdapter $adapterService, \Klink\DmsDocuments\DocumentsService $documentsService)
	{

		$this->middleware('auth');

		$this->middleware('capabilities');

		$this->service = $adapterService;
		$this->documentsService = $documentsService;
                    
        app()->bind('Illuminate\Contracts\Bus\Dispatcher', 'Illuminate\Bus\Dispatcher'); // TODO: needed anymore?
                
	}

	/**
	 * Display a listing of the resource and performing the download request
	 *
	 * @return Response
	 */
	public function index(Guard $auth, Request $request, $internal_json = false)
	{

        $auth_user = $auth->user();

        $resp = $this->documentsService->importStatus($auth_user);
        
        $resp['pagetitle'] = trans('import.page_title');

        if($internal_json || $request::wantsJson()){

            return response()->json($resp);

        }

		return view('documents.import', $resp);
	}


    /**
     * Clear the completed imports made by the logged-in user
     */
    public function clearCompleted(Guard $auth, Request $request)
    {
        $auth_user = $auth->user();

        \DB::transaction(function() use($auth_user){
            Import::completed($auth_user->id)->with('file')->delete();
        });

        if($request::wantsJson()){

            return response()->json(['status' => 'ok']);

        }

        return response('ok', 200);

    }

    /**
     * Handle the creation of an import job
     */
    public function store(Guard $auth, \KlinkDMS\Http\Requests\ImportRequest $request)
    {
   

        $auth_user = $auth->user();

        $currently_added_info = null;

        if ($request->wantsJson()){



            $this->user = $auth_user;

            $remote = $request->input('from') === "remote";

            try{
            
                if($remote){
                    $url = $request->input('remote_import');

                    $currently_added_info = $this->documentsService->importFromUrl($url, $auth_user);

                }
                else {
                    $url = $request->input('folder_import');
                    if(!is_dir($url)){
                        return new JsonResponse(array('folder_import' => trans('errors.import.folder_not_readable', ['folder' => $url])), 422);
                    }

                    $currently_added_info = $this->documentsService->importFromFolder($url, $auth_user, true, true);

                }


                $resp = $this->documentsService->importStatus($auth_user);

                if(!is_null($currently_added_info) && isset($currently_added_info['message'])){
                    $resp['status']['details'] = '<strong>'.$currently_added_info['message'] . '</strong>, ' . $resp['status']['details'];
                }
                
                return response()->json($resp);

            }catch(FileAlreadyExistsException $aex){

                \Log::error('Import store', ['exception' => $aex]);

                return new JsonResponse(array('' . (($remote) ? 'remote_import' : 'folder_import') => $aex->render($auth_user)), 422);

            }catch(\InvalidArgumentException $ex){

                \Log::error('Import store', ['exception' => $ex]);

                return new JsonResponse(array('' . (($remote) ? 'remote_import' : 'folder_import') => $ex->getMessage()), 422);

            }catch(\Exception $ex){

                \Log::error('Import store', ['exception' => $ex]);

                return new JsonResponse(array('' . (($remote) ? 'remote_import' : 'folder_import') => $ex->getMessage()), 422);

            }

            

        }

        return response('Format not supported.', 400);
    }
    
    
    public function update(Guard $auth, ImportUpdateRequest $request, $id){
        
        $user = $auth->user();
        
        $import = Import::with('file')->findOrFail($id);
        
        try{
            
            if(is_null($import->file)){
                throw new Exception(trans('import.retry.retry_error_file_not_found'));
            }
        
            if(!$user->isDMSManager() || ($user->id != $import->user_id)){
                
                if(is_null($import->file)){
                    throw new ForbiddenException(trans('import.retry.retry_forbidden_user_alternate'));
                }
                
                throw new ForbiddenException(trans('import.retry.retry_forbidden_user', ['import' => $import->file->name]));
                
            }
            
            if( !$import->isError() ){
                
                throw new ForbiddenException(trans('import.retry.forbidden_status'));
                
            }
            
            if( empty($import->job_payload) ){
                
                throw new Exception(trans('import.retry.retry_error_file_not_found'));
                
            }
            
            $import->status = Import::STATUS_QUEUED;
            $import->status_message = Import::MESSAGE_QUEUED;
            $import->save();
            
            // retry code here
            $failed_payload = $import->job_payload;
            $failed_payload = $this->resetAttempts($failed_payload);
            app('queue')->pushRaw($failed_payload, 'default');
            
            
            if($request->wantsJson()){
                
                return response()->json([
                    'status' => 'ok',
                    'message' => trans('import.retry.retry_completed_message', ['import' => $import->file->name])
                ]);

            }
            
            return response('Format not supported.', 400);
        
        }catch(ForbiddenException $ex){

            \Log::error('Import update', ['user_id' => $user->id, 'import_id' => $id, 'exception' => $ex]);

            return new JsonResponse( [
                    'status' => 'error',
                    'error' => $ex->getMessage()
                ], 422);

        }catch(\Exception $ex){

            \Log::error('Import update', ['user_id' => $user->id, 'import_id' => $id, 'exception' => $ex]);

            return new JsonResponse( [
                    'status' => 'error',
                    'error' => $ex->getMessage()
                ], 400);

        }
        
    }

    
    public function destroy(Guard $auth, \Illuminate\Http\Request $request, $id){
        
        $user = $auth->user();
        
        $import = Import::with('file')->findOrFail($id);
        
        try{
        
            if(!$user->isDMSManager() || ($user->id != $import->user_id)){
                
                if(is_null($import->file)){
                    throw new ForbiddenException(trans('import.remove.destroy_forbidden_user_alternate'));
                }
                
                throw new ForbiddenException(trans('import.remove.destroy_forbidden_user', ['import' => $import->file->name]));
                
            }
            
            if( !($import->isError() || $import->isCompleted()) ){
                
                throw new ForbiddenException(trans('import.remove.destroy_forbidden_status'));
                
            }
            
            $done = false;
            
            if( $import->isError() && !is_null($import->file) ){

                $done = $import->file->physicalDelete();
                
                if($done){
                    $done = $import->delete();
                }
                
            }
            else if( $import->isCompleted() ){
                
                $done = $import->delete();
                
            }
            
            $done = is_null($done) ? true : $done; // get the status of the model, because delete methods can return null
            
            
            if($request->wantsJson()){
                
                if(!$done){
                    
                    \Log::error('Import destroy', ['user_id' => $user->id, 'import_id' => $id, 'file_id' => is_null($import->file) ? $import->file : $import->file->id, 'exception' => 'FILE_OR_IMPORT_NOT_DELETED']);
                    
                    return new JsonResponse( [
                        'status' => 'error',
                        'error' => trans('import.remove.destroy_error', ['error' => 'File or Import delete not completed'])
                    ], 422);
                }

                return response()->json([
                    'status' => 'ok',
                    'message' => trans('import.remove.removed_message', ['import' => $import->file->name])
                ]);

            }
            
            return response('Format not supported.', 400);
        
        }catch(ForbiddenException $ex){

            \Log::error('Import destroy', ['user_id' => $user->id, 'import_id' => $id, 'exception' => $ex]);

            return new JsonResponse( [
                    'status' => 'error',
                    'error' => $ex->getMessage()
                ], 422);

        }catch(\Exception $ex){

            \Log::error('Import destroy', ['user_id' => $user->id, 'import_id' => $id, 'exception' => $ex]);

            return new JsonResponse( [
                    'status' => 'error',
                    'error' => $ex->getMessage()
                ], 500);

        }
        
    }
    
    
     /**
     * Reset the payload attempts.
     *
     * copied from https://github.com/laravel/framework/blob/2a38acf7ee2882d831a3b9a1361a710e70ffa31e/src/Illuminate/Queue/Console/RetryCommand.php
     *
     * @param  string  $payload
     * @return string
     */
    protected function resetAttempts($payload)
    {
        $payload = json_decode($payload, true);
        if (isset($payload['attempts'])) {
            $payload['attempts'] = 1;
        }
        return json_encode($payload);
    }
}
