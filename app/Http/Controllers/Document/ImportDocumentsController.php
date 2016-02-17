<?php namespace KlinkDMS\Http\Controllers\Document;

use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Import;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Validation\Validator;
use KlinkDMS\File;
use Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Bus\DispatchesCommands;
use KlinkDMS\Commands\ImportCommand;
use Illuminate\Support\Facades\Log;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use Illuminate\Http\JsonResponse;

class ImportDocumentsController extends Controller {

	// USER + DESCR ID (INST + LOCAL DOC ID)
	use DispatchesCommands;
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
	public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapterService, \Klink\DmsDocuments\DocumentsService $documentsService)
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
        /*
         * LOCAL SHARE FILE IMPORT
         * 
         * (1 file to download= 1 downloadjob in the queue, even folders, are pushed into the queue)
         * at each level (folder) it's called this function to push the download job in the Queue  
         * and to link the possible children folder to the parent_id (upper level)
         * 
         * starting from root to recursively explore the folders until I find all files and no folders
         * 
         */
        // private function level($import){
        //     $file = File::find($import->file_id);
        //     $files_in_the_folder = scandir($file->original_uri);
        //     if(is_dir($file->original_uri) && $files_in_the_folder){
        //         foreach($files_in_the_folder as $f){
        //             if($f=="." || $f==".."){
        //                 continue;
        //             }
        //             $file_child = new File();
        //             $file_child->name=$f;//lo modifica dopo la queue
        //             $file_child->hash='';//lo modifica dopo la queue
        //             $file_child->mime_type='';//lo modifica dopo la queue
        //             $file_child->size= is_dir($file->original_uri.$f) ? 1 : 0;
        //             $file_child->revision_of=null;
        //             $file_child->thumbnail_path=null;
        //             $file_child->path = uniqid();
        //             $file_child->user_id = $this->user->id;
        //             $dir_slash =is_dir($file->original_uri.$f) ? "/" : '';
        //             $file_child->original_uri = $file->original_uri."".$f. $dir_slash;
        //             $file_child->is_folder = is_dir($file->original_uri."".$f.$dir_slash);
        //             $file_child->save();


        //             $import_child = new Import();
        //             $import_child->bytes_expected = 1;
        //             $import_child->bytes_received = 1;
        //             $import_child->file_id = $file_child->id;
        //             $import_child->is_remote = false;
        //             $import_child->status = Import::STATUS_QUEUED;
        //             $import_child->user_id = $this->user->id;
        //             $import_child->parent_id = $import->id;
        //             $import_child->status_message = Import::MESSAGE_QUEUED;
        //             $import_child->save();

        //             Queue::push('ImportCommand@init', array('user' => $this->user,'import' => $import_child));
        //             if(is_dir($file->original_uri.$f)){
        //                 $this->level($import_child);//the child that becomes a parent...
        //             }
        //         }
        //     }else{
        //         /*
        //          * it is not a directory. just treat as normal url remote import if it's a file
        //          */
        //         if(is_file($file->original_uri)){
        //             Queue::push('ImportCommand@init', array('user' => $auth->user(),'import' => $import));
        //         }
        //     }
        // }
	

        /*
         * client update function about the status of the $import_id
         */
        // public function show($import_id, Guard $auth, \Request $request){
        //     if ($request::ajax() && $request::wantsJson() || getenv("APP_ENV")=="testing")
        //     {
        //         $root = Import::find($import_id)->first();
        //         $completed = 0;
        //         $expected = 0;
        //         $import_completed = array();
        //         $import_not_completed = array();
        //         $children = array();
        //         $file = File::find($root->file_id);
                
        //         if($root->is_remote){//remote single file import
        //             if($root->status == Import::STATUS_COMPLETED){  
        //                 array_push($import_completed,$root);
        //             }else{
        //                 array_push($import_not_completed,$root);
        //             }
        //             $expected = $root->bytes_expected;
        //             $completed = $root->bytes_received;
        //             $root->file = $file;
        //         }else{//shared import
        //             $this->childrenArray = array();
        //             $this->children($root->id);
        //             if(count($this->childrenArray)>0){
        //                 foreach($this->childrenArray as $c){
        //                     $completed += (int)$c->bytes_received;
        //                     $expected  += (int)$c->bytes_expected;
        //                     $f = File::find($c->file_id);
        //                     $c->file = $f;
        //                     if($c->status==Import::STATUS_COMPLETED){
        //                         array_push( $import_completed , $c);
        //                     }else{
        //                         array_push( $import_not_completed , $c);
        //                     }

        //                 }
        //             }
        //         }
        //         $files = count($import_completed)+count($import_not_completed);
        //         return response()->json(array(
        //             'result' => 'ok',
        //             "not_completed" => $import_not_completed,
        //             "completed" => $import_completed,
        //             "downloading" => $completed,
        //             'expected' => $expected,
        //             'msg' => $files !== 1 ? $files." files found" : "1 file found"
        //         ));
        //     }
        // }
        //solo per $this->status()
        // private function children($parent){
        //     $children = Import::myChildren($parent)->get();
        //     if(count($children)>0){
        //         foreach($children as $c){
        //             $file = File::find($c->file_id);
        //             if($file->is_folder){
        //                 $this->children($c->id);
        //             }else{
        //                 array_push($this->childrenArray, $c);
        //             }
        //         }
        //     }
        // }

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

                return new JsonResponse(array('' . (($remote) ? 'remote_import' : 'folder_import') => $aex->getMessage()), 422);

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
     
}
