<?php

namespace KBox\Http\Controllers\Document;

use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use KBox\DocumentDescriptor;
use KBox\Group;
use KBox\Capability;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KBox\Http\Requests\BulkDeleteRequest;
use KBox\Http\Requests\BulkMoveRequest;
use KBox\Http\Requests\BulkRestoreRequest;
use KBox\Http\Requests\BulkMakePublicRequest;
use KBox\Exceptions\ForbiddenException;
use Illuminate\Support\Collection;
use Klink\DmsAdapter\KlinkVisibilityType;
use KBox\Jobs\ReindexDocument;
use Illuminate\Support\Facades\DB;

class BulkController extends Controller
{

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
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\KBox\Documents\Services\DocumentsService $adapterService)
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
        try {
            $user = $auth->user();
            
            \Log::info('Bulk Deleting', ['triggered_by' => $user->id, 'params' => $request->all()]);
            
            $force = $request->input('force', false);
            
            // if($force && !$user->can_capability(Capability::CLEAN_TRASH)){
            // 	throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'));
            // }

            $that = $this;
            
            $all_that_can_be_deleted = $this->service->getUserTrash($user);

            // document delete
            
            $docs = $request->input('documents', []);
            
            if (! is_array($docs)) {
                $docs = [$docs];
            }
            
            if (empty($docs) && $force) {
                $docs =  $all_that_can_be_deleted->documents();
            }
            
            foreach ($docs as $document) {
                $that->deleteSingle($user, $document, $force);
            }

            $document_delete_count = count($docs);
            
            // group delete

            $grps = $request->input('groups', []);

            if (! is_array($grps)) {
                $grps = [$grps];
            }
            
            if (empty($grps) && $force) {
                $grps = $all_that_can_be_deleted->collections();
            }
            
            foreach ($grps as $grp) {
                $that->deleteSingleGroup($user, $grp, $force);
            }

            $group_delete_count = count($grps);
            
            // TODO: now it's time to submit to the queue the reindex job for each DocumentDescriptor
            // submit:
            // - documents affected by direct removal
            // - documents affected by group deletetion

            $count = ($document_delete_count + $group_delete_count);
            $message = $force ? trans_choice('documents.bulk.permanently_removed', $count, ['num' => $count]) : trans_choice('documents.bulk.removed', $count, ['num' => $count]);
            $status = ['status' => 'ok', 'message' =>  $message];

            \Cache::flush();

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 200);
            }

            return response('ok');
        } catch (\Exception $kex) {
            \Log::error('Bulk Deleting error', ['error' => $kex, 'user' => $auth->user(), 'request' => $request->all()]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.remove_error', ['error' => $kex->getMessage()])];

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }

    public function emptytrash(AuthGuard $auth, Request $request)
    {
        try {
            $user = $auth->user();
            
            \Log::info('Cleaning trash', ['triggered_by' => $user->id]);
            
            $all_that_can_be_deleted = $this->service->getUserTrash($user);

            // document delete
            
            $docs =  $all_that_can_be_deleted->documents();
            
            foreach ($docs as $document) {
                $this->service->permanentlyDeleteDocument($user, $document);
            }

            $grps = $all_that_can_be_deleted->collections();
            
            foreach ($grps as $grp) {
                $this->service->permanentlyDeleteGroup($grp, $user);
            }

            $count = ($docs->count() + $grps->count());
            $message = trans_choice('documents.bulk.permanently_removed', $count, ['num' => $count]);
            $status = ['status' => 'ok', 'message' =>  $message];

            \Cache::flush();

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 200);
            }

            return response('ok');
        } catch (\Exception $kex) {
            \Log::error('Trash Empty action error', ['error' => $kex, 'user' => $auth->user()]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.remove_error', ['error' => $kex->getMessage()])];

            if ($request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }
    
    private function deleteSingle($user, $id, $force = false)
    {
        $descriptor = ($id instanceof DocumentDescriptor) ? $id : DocumentDescriptor::withTrashed()->findOrFail($id);
            
        if ($descriptor->isPublic() && ! $user->can_capability(Capability::PUBLISH_TO_KLINK)) {
            \Log::warning('User tried to delete a public document without permission', ['user' => $user->id, 'document' => $id]);
            throw new ForbiddenException(trans('documents.messages.delete_public_forbidden'), 2);
        }
        
        // if($force && !$user->can_capability(Capability::CLEAN_TRASH)){
        // 	\Log::warning('User tried to force delete a document without permission', ['user' => $user->id, 'document' => $id]);
        // 	throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
        // }
        
        \Log::info('Deleting Document', ['params' => $id]);
    
        if (! $force) {
            return $this->service->deleteDocument($user, $descriptor);
        } else {
            return $this->service->permanentlyDeleteDocument($user, $descriptor);
        }
    }
    
    private function deleteSingleGroup($user, $id, $force = false)
    {
        $group = ($id instanceof Group) ? $id : Group::withTrashed()->findOrFail($id);
        
        if ($force && ! $user->can_capability(Capability::CLEAN_TRASH)) {
            \Log::warning('User tried to force delete a group without permission', ['user' => $user->id, 'document' => $id]);
            throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
        }
            
        if (! is_null($group->project)) {
            throw new ForbiddenException(trans('projects.errors.prevent_delete_description'));
        }
        
        \Log::info('Deleting group', ['params' => $id]);
    
        if (! $force) {
            $this->service->deleteGroup($user, $group);
        } else {
            return $this->service->permanentlyDeleteGroup($group, $user);
        }
    }
    
    public function restore(AuthGuard $auth, BulkRestoreRequest $request)
    {
        try {
            \Log::info('Bulk Restoring', ['params' => $request]);
        
            //			$user = $auth->user();
                
            $that = $this;

            $status = DB::transaction(function () use ($request, $that, $auth) {
                $docs = $request->input('documents', []);
                $grps = $request->input('groups', []);

                foreach ($docs as $document) {
                    $that->service->restoreDocument(DocumentDescriptor::onlyTrashed()->findOrFail($document));
                }

                if (! empty($grps)) {
                    foreach ($grps as $grp) {
                        $g = Group::onlyTrashed()->findOrFail($grp);
                        $g->restoreFromTrash();
                    }
                }

                $count = (count($docs) + count($grps));
                return ['status' => 'ok', 'message' =>  trans_choice('documents.bulk.restored', $count, ['num' => $count])];
            });
            
            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 200);
            }

            return response('ok', 202);
        } catch (\Exception $kex) {
            \Log::error('Document restoring error', ['error' => $kex, 'request' => $request]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.restore_error', ['error' => $kex->getMessage()])];

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }

    /**
     * Bulk copy to Collection
     * @param  AuthGuard         $auth    [description]
     * @param  BulkDeleteRequest $request [description]
     * @return Response
     */
    public function copyTo(AuthGuard $auth, BulkMoveRequest $request)
    {

        // ids might be comma separated, single transaction

        \Log::info('Bulk CopyTo', ['params' => $request->all()]);

        try {
            $docs = $request->input('documents', []);
            $grps = $request->input('groups', []);

            $add_to = $request->input('destination_group', 0);

            $add_to_this_group = Group::findOrFail($add_to);

            $already_added = $add_to_this_group->documents()->whereIn('document_descriptors.id', $docs)->get(['document_descriptors.*'])->pluck('id')->toArray();
            
            $already_there_from_this_request = array_intersect($already_added, $docs);
            
            $count_docs_original = count($docs);
            $docs = array_diff($docs, $already_added); //removes already added docs from the list
            
            $documents = DocumentDescriptor::whereIn('id', $docs)->get();

            $this->service->addDocumentsToGroup($auth->user(), $documents, $add_to_this_group, false);
            
            $documents->each(function ($document) {
                dispatch(new ReindexDocument($document, KlinkVisibilityType::KLINK_PRIVATE));
            });
 
            $status = [
                'status' => ! empty($already_there_from_this_request) ? 'partial' : 'ok',
                'title' =>  ! empty($already_there_from_this_request) ?
                    trans_choice('documents.bulk.some_added_to_collection', count($docs), []) :
                    trans('documents.bulk.added_to_collection'),
                'message' =>  ! empty($already_there_from_this_request) ?
                    trans_choice('documents.bulk.copy_completed_some', count($docs), ['count' => count($docs), 'collection' => $add_to_this_group->name, 'remaining' => $count_docs_original - count($docs)]) :
                    trans('documents.bulk.copy_completed_all', ['collection' => $add_to_this_group->name])
            ];
            
            if ($request->wantsJson()) {
                return new JsonResponse($status, 200);
            }

            return response('ok');
        } catch (\Exception $kex) {
            \Log::error('Bulk Copy to error', ['error' => $kex, 'request' => $request->all()]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.copy_error', ['error' => $kex->getMessage()])];

            if ($request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }
    
    // public function makePublicDialog(AuthGuard $auth, BulkDeleteRequest $request)
    // {
    //     // for the dialog in case some documents needs a rename ?
    // }
    
    /**
     * Make documents and collections public on the network
     */
    public function makePublic(AuthGuard $auth, BulkMakePublicRequest $request)
    {
        \Log::info('Bulk Make Public', ['params' => $request->all()]);

        try {
            $that = $this;

            $status = DB::transaction(function () use ($request, $that, $auth) {
                $docs = $request->input('documents', []);
                $grp = $request->input('group', null);
                
                $documents = new Collection;
                
                if (! empty($docs)) {
                    $documents = DocumentDescriptor::whereIn('id', $docs)->get();
                }
                
                if (! is_null($grp)) {
                    $group_docs = Group::findOrFail($grp)->documents()->get();
                    $documents = $documents->merge($group_docs)->unique();
                }
                
                foreach ($documents as $descriptor) {
                    $descriptor->publish($request->user());
                }

                $count = $documents->count();
                return ['status' => 'ok', 'message' =>  trans_choice('networks.made_public', $count, ['num' => $count, 'network' => network_name() ])];
            });
            
            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 200);
            }

            return response('ok');
        } catch (\Exception $kex) {
            \Log::error('Bulk Make Public error', ['error' => $kex, 'request' => $request]);

            $status = ['status' => 'error', 'message' =>  trans('networks.make_public_error', ['error' => $kex->getMessage()])];

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }

    /**
     * Remove documents from the network by making them private
     */
    public function makePrivate(AuthGuard $auth, BulkMakePublicRequest $request)
    {
        \Log::info('Bulk Make Private', ['params' => $request->all()]);

        try {
            $that = $this;

            $status = DB::transaction(function () use ($request, $that, $auth) {
                $docs = $request->input('documents', []);
                $grp = $request->input('group', null);
                
                $documents = new Collection;
                
                if (! empty($docs)) {
                    $documents = DocumentDescriptor::whereIn('id', $docs)->get();
                }
                
                if (! is_null($grp)) {
                    $group_docs = Group::findOrFail($grp)->documents()->get();
                    $documents = $documents->merge($group_docs)->unique();
                }
                
                foreach ($documents as $descriptor) {
                    $descriptor->unpublish($request->user());
                }

                $count = $documents->count();
                return ['status' => 'ok', 'message' =>  trans_choice('networks.made_private', $count, ['num' => $count, 'network' => network_name() ])];
            });

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 200);
            }

            return response('ok');
        } catch (\Exception $kex) {
            \Log::error('Bulk Make Public error', ['error' => $kex, 'request' => $request]);

            $status = ['status' => 'error', 'message' =>  trans('networks.make_public_error', ['error' => $kex->getMessage()])];

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }
}
