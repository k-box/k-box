<?php namespace KlinkDMS\Http\Controllers\Document;

use KlinkDMS\Http\Requests;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Group;
use KlinkDMS\Capability;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KlinkDMS\Http\Requests\BulkDeleteRequest;
use KlinkDMS\Http\Requests\BulkRestoreRequest;
use KlinkDMS\Http\Requests\BulkMakePublicRequest;
use KlinkDMS\Exceptions\ForbiddenException;
use Illuminate\Support\Collection;

class BulkController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Bulk Operation on Documents and Groups Controller
	|--------------------------------------------------------------------------
	|
	| handle the operation when something is performed on a multiple selection.
	| To simply JS stuff
	|
	*/

	/**
	 * [$service description]
	 * @var \Klink\DmsDocuments\DocumentsService
	 */
	private $service = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService)
	{
            
		$this->middleware('auth');

		$this->middleware('capabilities');

		$this->service = $adapterService;
	}


	/**
	 * Bulk delete over documents and groups.
	 * If a single operation fails all the delete is aborted
	 *
	 * @return Response
	 */
	public function destroy(AuthGuard $auth, BulkDeleteRequest $request)
	{

		// ids might be comma separated, single transaction

		\Log::info('Bulk Deleting', ['params' => $request]);

		try{
			
			$user = $auth->user();
			
			$force = $request->input('force', false);
			
			// if($force && !$user->can(Capability::CLEAN_TRASH)){
			// 	throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'));
			// }

			$that = $this;
			
			$all_that_can_be_deleted = $this->service->getUserTrash($user);

			$document_delete_count = \DB::transaction(function() use($request, $that, $user, $all_that_can_be_deleted, $force){
				
				// Documents delete transaction

				$docs = $request->input('documents', array());
				
				if(!is_array($docs)){
					$docs = array($docs);
				}
				
				if(empty($docs) && $force){
					$docs =  $all_that_can_be_deleted->documents();
				}
				
				foreach ($docs as $document) {
					$that->deleteSingle($user, $document, $force);
				}

				return count($docs);
			});

			$group_delete_count = \DB::transaction(function() use($request, $that, $user, $all_that_can_be_deleted, $force){
				
				// Collections delete transaction

				$grps = $request->input('groups', array());

				if(!is_array($grps)){
					$grps = array($grps);
				}
				
				if(empty($grps) && $force){
					$grps = $all_that_can_be_deleted->collections();
				}
				
				foreach ($grps as $grp) {
					$that->deleteSingleGroup($user, $grp, $force);
				}

				return count($grps);
			});

			$count = ($document_delete_count + $group_delete_count);
			$message = $force ? trans_choice('documents.bulk.permanently_removed', $count, ['num' => $count]) : trans_choice('documents.bulk.removed', $count, ['num' => $count]);
			$status = array('status' => 'ok', 'message' =>  $message);


			\Cache::flush();


			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 200);
			}

			return response('ok');

		}catch(\Exception $kex){

			\Log::error('Bulk Deleting error', ['error' => $kex, 'request' => $request]);

			$status = array('status' => 'error', 'message' =>  trans('documents.bulk.remove_error', ['error' => $kex->getMessage()]));

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 422);
			}

			return response('error');
			
		}

	}
	
	private function deleteSingle($user, $id, $force = false){
		
		$descriptor = ($id instanceof DocumentDescriptor) ? $id : DocumentDescriptor::withTrashed()->findOrFail($id);
			
		if($descriptor->isPublic() && !$user->can(Capability::CHANGE_DOCUMENT_VISIBILITY)){
			\Log::warning('User tried to delete a public document without permission', ['user' => $user->id, 'document' => $id]);
			throw new ForbiddenException(trans('documents.messages.delete_public_forbidden'), 2);
		}
		
		// if($force && !$user->can(Capability::CLEAN_TRASH)){
		// 	\Log::warning('User tried to force delete a document without permission', ['user' => $user->id, 'document' => $id]);
		// 	throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
		// }
		
		\Log::info('Deleting Document', ['params' => $id]);
	
		if(!$force){
			return $this->service->deleteDocument($descriptor);
		}
		else {
			return $this->service->permanentlyDeleteDocument($descriptor);
		}
	}
	
	private function deleteSingleGroup($user, $id, $force = false){
		
		$group = ($id instanceof Group) ? $id : Group::withTrashed()->findOrFail($id);
		
		if($force && !$user->can(Capability::CLEAN_TRASH)){
			\Log::warning('User tried to force delete a group without permission', ['user' => $user->id, 'document' => $id]);
			throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
		}
			
		if(!is_null($group->project)){
			
			throw new ForbiddenException(trans('projects.errors.prevent_delete_description'));
			
		}
		
		\Log::info('Deleting group', ['params' => $id]);
	
		if(!$force){
			$this->service->deleteGroup($user, $group);
		}
		else {
			return $this->service->permanentlyDeleteGroup($group, $user);
		}
	}
	
	public function restore(AuthGuard $auth, BulkRestoreRequest $request){
		
		try{
			
			\Log::info('Bulk Restoring', ['params' => $request]);
		
//			$user = $auth->user();
				
			$that = $this;

			$status = \DB::transaction(function() use($request, $that, $auth){

				$docs = $request->input('documents', array());
				$grps = $request->input('groups', array());

				foreach ($docs as $document) {
					$that->service->restoreDocument(DocumentDescriptor::onlyTrashed()->findOrFail($document));
				}

				if(!empty($grps)){
					foreach ($grps as $grp) {
						$g = Group::withTrashed()->findOrFail($grp);
						$g->restore();
					}
				}

				$count = (count($docs) + count($grps));
				return array('status' => 'ok', 'message' =>  trans_choice('documents.bulk.restored', $count, ['num' => $count]));
			});
			

			

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 200);
			}

			return response('ok', 202);
			
		
		}catch(\Exception $kex){

			\Log::error('Document restoring error', ['error' => $kex, 'request' => $request]);

			$status = array('status' => 'error', 'message' =>  trans('documents.bulk.restore_error', ['error' => $kex->getMessage()]));

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 422);
			}

			return response('error');
			
		}
		
	}

	/**
	 * Bulk copy to Collection
	 * @param  AuthGuard         $auth    [description]
	 * @param  BulkDeleteRequest $request [description]
	 * @return [type]                     [description]
	 */
	public function copyTo(AuthGuard $auth, BulkDeleteRequest $request)
	{

		// ids might be comma separated, single transaction

		\Log::info('Bulk CopyTo', ['params' => $request]);

		try{

			$that = $this;

			$status = \DB::transaction(function() use($request, $that, $auth){

				$docs = $request->input('documents', array());
				$grps = $request->input('groups', array());

				$add_to = $request->input('destination_group', 0);

				$add_to_this_group = Group::findOrFail($add_to);

				$documents = DocumentDescriptor::whereIn('id', $docs)->get();

				$that->service->addDocumentsToGroup($auth->user(), $documents, $add_to_this_group);

				$count = (count($docs) + count($grps));
				return array('status' => 'ok', 'message' =>  'ok');
			});




			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 200);
			}

			return response('ok');

		}catch(\Exception $kex){

			\Log::error('Bulk Deleting error', ['error' => $kex, 'request' => $request]);

			$status = array('status' => 'error', 'message' =>  trans('documents.bulk.copy_error', ['error' => $kex->getMessage()]));

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 422);
			}

			return response('error');
			
		}

	}
	
	
	public function makePublicDialog(AuthGuard $auth, BulkDeleteRequest $request){
		// for the dialog in case some documents needs a rename ?
	}
	
	public function makePublic(AuthGuard $auth, BulkMakePublicRequest $request){
		
		
		\Log::info('Bulk Make Public', ['params' => $request]);

		try{

			$that = $this;

			$status = \DB::transaction(function() use($request, $that, $auth){

				$docs = $request->input('documents', array());
				$grp = $request->input('group', null);
				
				$documents = new Collection;
				
				if(!empty($docs)){
					$documents = DocumentDescriptor::whereIn('id', $docs)->get();
				}
				
				if(!is_null($grp)){
					$group_docs = Group::findOrFail($grp)->documents()->get();
					$documents = $documents->merge($group_docs)->unique();
				}
				
				foreach($documents as $descriptor){
					if(!$descriptor->isPublic()){
						$descriptor->is_public = true;
						$descriptor->save();
						$that->service->reindexDocument($descriptor, \KlinkVisibilityType::KLINK_PUBLIC);
					}
				}

				$count = $documents->count();
				return array('status' => 'ok', 'message' =>  trans_choice('documents.bulk.make_public', $count, ['num' => $count]));
			});




			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 200);
			}

			return response('ok');

		}catch(\Exception $kex){

			\Log::error('Bulk Make Public error', ['error' => $kex, 'request' => $request]);

			$status = array('status' => 'error', 'message' =>  trans('documents.bulk.make_public_error', ['error' => $kex->getMessage()]));

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 422);
			}

			return response('error');
			
		}
		
	}

}
