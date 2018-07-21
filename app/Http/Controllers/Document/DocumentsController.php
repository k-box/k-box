<?php

namespace KBox\Http\Controllers\Document;

use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use KBox\DocumentDescriptor;
use KBox\Shared;
use KBox\Group;
use KBox\Capability;
use KBox\Project;
use KBox\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KBox\Http\Requests\DocumentAddRequest;
use KBox\Http\Requests\DocumentUpdateRequest;
use KBox\Exceptions\FileNamingException;
use KBox\Exceptions\ForbiddenException;
use Illuminate\Support\Collection;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KBox\Traits\Searchable;
use KBox\Events\UploadCompleted;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Exceptions\KlinkException;
use KBox\Jobs\ReindexDocument;
use KBox\Jobs\UpdatePublishedDocumentJob;

class DocumentsController extends Controller
{
    use Searchable;

    /*
    |--------------------------------------------------------------------------
    | Documents Controller
    |--------------------------------------------------------------------------
    |
    | Handle all the stuff related to document add, edit, remove,...
    |
    */

    /**
     * [$adapter description]
     * @var \Klink\DmsAdapter\KlinkAdapter
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService)
    {
        $this->middleware('auth', ['except' => ['showByKlinkId']]);

        $this->middleware('capabilities', ['except' => ['showByKlinkId']]);

        $this->service = $adapterService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(AuthGuard $auth, Request $request, $visibility = 'private')
    {
        $user = $auth->user();

        if (! $user->isDMSManager() && $visibility==='private') {
            $visibility = 'personal';
        }
        
        $filtered_ids = false;
        $pagination = false;
        $showing_only_local_public = false;
        
        $is_personal = $visibility === 'personal' ? true : false;
        if ($is_personal) {
            $visibility = 'private';
        }

        $req = $this->searchRequestCreate($request);
        
        $req->visibility($visibility);
        
        $results = $this->search($req, function ($_request) use ($is_personal, $user) {
            if ($_request->visibility === KlinkVisibilityType::KLINK_PUBLIC) {
                // if public => return direct search because we want them to see the public network
                return false;
            }
            
            if ($is_personal) {
                $personal_doc_id = DocumentDescriptor::local()->private()->ofUser($user->id)->get()->map->uuid;
                
                $_request->in($personal_doc_id);
            }
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = DocumentDescriptor::local();
                
                $_request->setForceFacetsRequest();
            
                if ($_request->visibility === KlinkVisibilityType::KLINK_PRIVATE) {
                    $all_query = $all_query->private();
                    if ($is_personal) {
                        $all_query = $all_query->ofUser($user->id);
                    }
                }
                
                return $all_query->orderBy('title', 'ASC');
            }
            
            return false; // force to execute a search on the core instead on the database
        });

        // Adding user's root groups and institution level groups to the result
        // $groups = Group::roots()->private($auth->user()->id)->orPublic()->get();

        return view('documents.documents', [
            'pagetitle' => (is_null($visibility) ? '': ($visibility === 'public' ? network_name().' ' : trans('documents.menu.'.($is_personal ? 'personal' : $visibility)).' ')).trans('documents.page_title'),
            'documents' => $results ? $results->getCollection() : collect(),
            'context' => is_null($visibility) ? 'all' : $visibility,
            'pagination' => $results,
            'is_search_failed' => $results === null,
            'search_terms' => $req->term,
            'facets' => $results !== null ? $results->facets() : [],
            'filters' => $results !== null ? $results->filters() : [],
            'current_visibility' => $is_personal ? 'private' : $visibility,
            'is_personal' => $is_personal,
            'hint' => $showing_only_local_public ? trans('documents.messages.local_public_only') : false,
            'filter' => $visibility === 'public' ? network_name() : trans('documents.menu.'.($is_personal ? 'personal' : $visibility))
            ]);
    }

    public function trash(AuthGuard $auth)
    {
        $user = $auth->user();

        $all = $this->service->getUserTrash($user)->all();

        return view('documents.trash', [
            'search_terms' => '',
            'pagetitle' => trans('documents.menu.trash'),
            'documents' => $all,
            'context' => 'trash',
            'filter' => trans('documents.menu.trash'),
            'empty_message' => trans('documents.trash.empty_trash')
        ]);
    }

    public function notIndexed(AuthGuard $auth)
    {
        $all_query = DocumentDescriptor::local();

        //		if(!$auth->user()->isContentManager()){
        //			$all_query = $all_query->ofUser($auth->user()->id);
        //		}

        $all = $all_query->notIndexed()->get();

        return view('documents.documents', ['pagetitle' => trans('documents.menu.not_indexed'), 'documents' => $all, 'context' => 'notindexed', 'filter' => trans('documents.menu.not_indexed'), 'empty_message' => 'All the documents has been correctly added to K-Link.']);
    }

    public function sharedWithMe(AuthGuard $auth, Request $request)
    {
        
        // $with_me = null; /*$by_me = null;*/
        
        $auth_user = $auth->user();
        
        $can_share_with_personal = $auth_user->can_capability(Capability::SHARE_WITH_PERSONAL);

        $can_share_with_private = $auth_user->can_capability(Capability::SHARE_WITH_PRIVATE);
            
        $can_see_share = $auth_user->can_capability(Capability::RECEIVE_AND_SEE_SHARE);

        $order = $request->input('o', 'd') === 'a' ? 'ASC' : 'DESC';
        
        $req = $this->searchRequestCreate($request);
        
        $req->visibility('private');
        
        $with_me = $this->search($req, function ($_request) use ($auth_user, $can_share_with_personal, $can_share_with_private, $can_see_share, $order) {
            if (! $can_see_share) {
                return new Collection();
            }
            
            $group_ids = $auth_user->involvedingroups()->get(['peoplegroup_id'])->pluck('peoplegroup_id')->toArray();
                    
            $all_in_groups = Shared::sharedWithGroups($group_ids)->orderBy('created_at', $order)->get();
                
            $all_single = Shared::sharedWithMe($auth_user)->with(['shareable', 'sharedwith'])->orderBy('created_at', $order)->get();
            
            $all_shared = $all_single->merge($all_in_groups)->unique();
            
            $shared_docs = $all_shared->pluck('shareable.uuid')->all();

            $_request->in($shared_docs);
            
            if ($_request->isPageRequested()) {
                $_request->setForceFacetsRequest();
                
                return $all_shared;
            }
            
            return false; // force to execute a search on the core instead on the database
        });

        return view('documents.sharedwithme', [
            'pagetitle' => trans('documents.menu.shared'),
            'shared_with_me' => $with_me,
            'current_visibility' => 'private',
            // 'shared_by_me' => $by_me,
            'can_share' => $can_share_with_personal || $can_share_with_private,
            'context' => 'shared',
            'filter' => trans('documents.menu.shared'),
            'pagination' => $with_me,
            'order' => $order,
            'search_terms' => $req->term,
            'facets' => $with_me->facets(),
            'filters' => $with_me->filters(),
            'empty_message' => trans('share.empty_message')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AuthGuard $auth)
    {
        $user = $auth->user();

        $visibility = 'private';
        if (! $user->isDMSManager()) {
            $visibility = 'personal';
        }
        
        return view('documents.create', [
                'pagetitle' => trans('documents.create.page_title'),
                'context' => $visibility,
                'filter' => $visibility]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(AuthGuard $auth, DocumentAddRequest $request)
    {
        // ok here will arrive the file data and some info extracted by the JS
        // context info, like the visibility and maybe the group
        //
        // private visibility as default

        try {
            \Log::info('DocumentsController store', ['request' => $request->all(), 'user' => $auth->user()]);

            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                $grp = $request->has('group') ? Group::findOrFail($request->input('group')) : null;
                $parent = $grp;

                if ($request->has('folder_path')) {
                    $folder_path = $request->input('folder_path');
                    $parent = $this->service->createGroupsFromFolderPath($auth->user(), $folder_path, true, is_null($grp) ? true : $grp->is_private, $grp);
                }

                //test and report exceptions
                $descr = $this->service->importFile($request->file('document'), $auth->user(), 'private', $parent);

                event(new UploadCompleted($descr, $auth->user()));
                
                if ($request->wantsJson()) {
                    if (! is_array($descr)) {
                        $descr = ['descriptor' => $descr->fresh()];
                    }
                    
                    return response()->json($descr);
                } else {
                    return $this->show($descr->id, $auth);
                }
            } elseif ($request->hasFile('document') && ! $request->file('document')->isValid()) {
                if ($request->wantsJson()) {
                    return new JsonResponse(['error' => trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()])], 400);
                }
                
                return redirect()->back()->withErrors([
                    'error' => trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()])
                ]);
            }

            if ($request->wantsJson()) {
                return new JsonResponse(['error' => trans('errors.unknown')], 400);
            }

            return redirect()->back()->withErrors([
                'error' => trans('errors.unknown')
            ]);
        } catch (FileNamingException $ex) {
            \Log::warning('DocumentsController store - File Naming Policy check', ['error' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $ex->getMessage()], 409);
            }
            
            return redirect()->back()->withErrors([
                'error' => $ex->getMessage()
            ]);
        } catch (\Exception $ex) {
            \Log::warning('DocumentsController store - '.$ex->getMessage(), ['error' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $ex->getMessage()], 500);
            }

            return redirect()->back()->withErrors([
                'error' => $ex->getMessage()
            ]);
        }

        // available methods on file http://api.symfony.com/2.5/Symfony/Component/HttpFoundation/File/UploadedFile.html
    }

    private function _showPanel(DocumentDescriptor $document, $auth_user = null)
    {

        // building up the information on who has access to this document

        $access = [];

        if (! is_null($auth_user)) {
            if ($document->isMine()) {
                $existing_shares = $document->shares()->sharedByMe($auth_user)->where('sharedwith_type', 'KBox\User')->count();
                $public_link_shares = $document->shares()->where('sharedwith_type', 'KBox\PublicLink')->count();
                
                $users_from_projects = $this->service->getUsersWithAccess($document, $auth_user);
                $users_from_projects_count = $users_from_projects->count();

                $access_count_total = $existing_shares+$users_from_projects_count;

                $project_names = implode(', ', Project::whereIn('id', $users_from_projects->pluck('pivot.project_id')->unique())->get(['name'])->pluck('name')->toArray());

                if ($access_count_total === 0) {
                    $access[] = trans('panels.access.only_you');
                } elseif ($existing_shares > 0 && $users_from_projects_count === 0) {
                    $access[] = trans_choice('panels.access.you_and_direct', $existing_shares, ['num' => $existing_shares]);
                } elseif ($existing_shares === 0 && $users_from_projects_count > 0) {
                    $access[] = trans('panels.access.only_project_members', ['projects' => $project_names]);
                } else {
                    $access[] = trans_choice('panels.access.project_members_and_shares', $existing_shares, ['projects' => $project_names, 'num' => $existing_shares]);
                }
            
                if ($document->isPublic()) {
                    $access[] = trans('panels.access.network', ['network' => network_name()]);
                } elseif ($public_link_shares > 0) {
                    $access[] = trans('panels.access.public');
                } else {
                    $access[] = trans('panels.access.internal');
                }
            } else {
                $access[] = trans('panels.access.network', ['network' => network_name()]);
            }
        }

        return view('panels.document', [
            'item' => $document,
            'access' => $access,
            'access_by_count' => isset($access_count_total) ? $access_count_total : 0
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id, AuthGuard $auth, Request $request)
    {
        try {
            $document = DocumentDescriptor::withTrashed()->findOrFail($id);

            if ($request->ajax()) {
                return $this->_showPanel($document, $auth->user());
            }

            $url = \KBox\RoutingHelpers::preview($document);

            return redirect($url);
        } catch (ModelNotFoundException $kex) {
            \Log::warning('Document Descriptor not found', ['error' => $kex, 'id' => $id]);

            if ($request->ajax()) {
                return view('panels.error', ['error_title' => trans('errors.404_title'), 'message' => $kex->getMessage()]);
            }
            
            throw $kex;
        } catch (ForbiddenException $kex) {
            \Log::warning('Document Descriptor not accessible by user', ['error' => $kex, 'id' => $id, 'user' => $auth->user()->id]);
            
            return view('panels.error', ['error_title' => trans('errors.403_title'), 'message' => trans('errors.forbidden_see_document_exception')]);
        } catch (\Exception $kex) {
            \Log::error('Document Descriptor panel show error', ['error' => $kex, 'id' => $id]);
            
            if ($request->ajax()) {
                return view('panels.error', ['message' => $kex->getMessage()]);
            }

            throw $kex;
        }
    }

    public function showByKlinkId($institution, $local_id, AuthGuard $auth)
    {
        try {
            $document = $this->service->getDocument($local_id);

            return $this->_showPanel($document, $auth()->user());
        } catch (KlinkException $kex) {
            \Log::error('Document Descriptor showByKlinkId error', ['error' => $kex, 'institution' => $institution, 'local_id' => $local_id]);
            return view('panels.error', ['message' => $kex->getMessage()]);
        } catch (\Exception $kex) {
            \Log::error('Document Descriptor showByKlinkId error', ['error' => $kex, 'institution' => $institution, 'local_id' => $local_id]);
            return view('panels.error', ['message' => $kex->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id, AuthGuard $auth)
    {
        try {
            /*
             enable edit view only if the user
             - the owner of the document
             - has access to one the collection containing the document
            */
            $document = DocumentDescriptor::withTrashed()->findOrFail($id);
            $user = $auth->user();
            
            $is_owner = $document->owner_id === $user->id;
            
            // collections in which the document is and that can be seen by the user
            $collections = $this->service->getDocumentCollections($document, $user)->count();
            
            // if( !$is_owner && $collections === 0 && !$document->isShared() ){
            //
            //     throw new ForbiddenException( trans('errors.forbidden_edit_document_exception') , 403);
            // }
            
            $view_params = [
                    'document' => $document,
                    'file' => $document->file,
                    'can_make_public' => ! $document->trashed() && $user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY),
                    'can_edit_groups' => ! $document->trashed() && $user->can_capability([Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS]),
                    'can_upload_file' => ! $document->trashed() && $user->can_capability(Capability::UPLOAD_DOCUMENTS),
                    'can_edit_document' => ! $document->trashed() && $user->can_capability([Capability::EDIT_DOCUMENT, Capability::DELETE_DOCUMENT]),
                    // 'versions' => ! is_null($document->file) ? $document->file->revisionOfRecursive()->get() : new Collection,
                    'pagetitle' => trans('documents.edit.page_title', ['document' => $document->title]),
                    'context' => 'document', 'context_document' => $document->id, 'filter' => $document->name,
                    'duplicates' => $document->duplicates()->of($user)->notResolved()->get()
                ];

            return view('documents.edit', $view_params);
        } catch (ForbiddenException $kex) {
            \Log::warning('User tried to edit a document who don\'t has access to', ['error' => $kex, 'user' => $auth->user()->id, 'document' => $id]);
            
            throw $kex;
        } catch (\Exception $kex) {
            \Log::error('Error generating data for documents.edit view', ['error' => $kex]);
            
            // return view('panels.error', ['message' => $kex->getMessage()]);
            throw $kex;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, AuthGuard $auth, DocumentUpdateRequest $request)
    {

        // single descriptor
        // can remove group
        // can add group
        // can change title
        // can change visibility
        // can ...
        // title: string
        // mime_type: string
        // visibility: string
        
        // user_owner: string
        // user_uploader: string
        // abstract: string
        // language: string
        // authors: string (serialized)
        // file_id: File
        // owner_id: User
        // created_at
        // updated_at

        \Log::info('Updating Document', ['params' => $id, 'request' => $request->all()]);

        try {
            $user = $auth->user();

            if (! $user->can_capability(Capability::EDIT_DOCUMENT)) {
                throw new ForbiddenException(trans('documents.messages.forbidden'), 1);
            }

            $serv = $this->service;

            $ret = \DB::transaction(function () use ($id, $serv, $request, $user) {
                $document = DocumentDescriptor::findOrFail($id);

                $group_dirty = false;

                // 'authors' => 'sometimes|required|string|regex:/^[\w\d\s\.\-_\(\)]*/',
                // 'visibility' => 'sometimes|required|string|in:public,private',
                
                //    // if this is present a new file version will be created and will inherit the
                // 'document' => 'sometimes|required|between:0,30000', //new document version
                
                // 'remove_group' => 'sometimes|required|exists:groups,id',
                // 'add_group' => 'sometimes|required|exists:groups,id',
                
                if ($request->has('remove_group')) {
                    $remove_from_group = $request->input('remove_group');
                     
                    if (! is_array($remove_from_group)) {
                        $remove_from_group = [$remove_from_group];
                    }
                     
                    foreach ($remove_from_group as $remove_from) {
                        $grp = Group::findOrFail($remove_from);
                         
                        $serv->removeDocumentFromGroup($user, $document, $grp, false);
                    }
                     
                    $group_dirty = true;
                }
                
                if ($request->has('add_group')) {
                    $add_to_group = $request->input('add_group');
                    
                    if (! is_array($add_to_group)) {
                        $add_to_group = [$add_to_group];
                    }
                    
                    foreach ($add_to_group as $add_to) {
                        $grp = Group::findOrFail($add_to);
                    
                        $serv->addDocumentToGroup($user, $document, $grp, false);
                    }
                    
                    $group_dirty = true;
                }

                if ($request->has('title') && $request->input('title') !== $document->title) {
                    $document->title = e($request->input('title'));
                }

                if (($request->has('abstract') || $request->input('abstract', false) === '') && $request->input('abstract') !== $document->abstract) {
                    $document->abstract = e($request->input('abstract'));
                }

                if ($request->has('language') && $request->input('language') !== $document->language) {
                    $document->language = e($request->input('language'));
                }

                $is_json = $request->isJson();

                if ($request->has('authors') && $request->input('authors') !== $document->authors) {
                    $document->authors = e($request->input('authors'));
                }
                
                if ($request->has('copyright_usage') && $request->input('copyright_usage') !== $document->copyright_usage->id) {
                    $document->copyright_usage = e($request->input('copyright_usage'));
                }

                if ($request->exists('copyright_owner_name') ||
                    $request->exists('copyright_owner_email') ||
                    $request->exists('copyright_owner_website') ||
                    $request->exists('copyright_owner_address')) {
                    $document->copyright_owner = collect([
                        'name' => e($request->input('copyright_owner_name', '')),
                        'email' => e($request->input('copyright_owner_email', '')),
                        'website' => e($request->input('copyright_owner_website', '')),
                        'address' => e($request->input('copyright_owner_address', '')),
                    ]);
                }
                
                // handle new file version
                $uploaded_new_version = false;
                
                if ($request->hasFile('document') && $request->file('document')->isValid()) {
                    \Log::info('Update Document with new version');
    
                    //test and report exceptions
                    $file_model = $this->service->createFileFromUpload($request->file('document'), $user, $document->file);

                    $document->file_id = $file_model->id;
                    $document->mime_type = $file_model->mime_type;
                    $document->document_type = KlinkDocumentUtils::documentTypeFromMimeType($file_model->mime_type);
                    $document->hash = $file_model->hash;
                    $uploaded_new_version = true;
                } elseif ($request->hasFile('document')) {
                    throw new Exception(trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()]), 400);
                }

                // save everything if the descriptor isDirty and do the reindex if necesary
                
                if ($document->isDirty() || $group_dirty) {
                    $document->save();
                    
                    if ($document->isFileUploadComplete()) {
                        $document->status = DocumentDescriptor::STATUS_PROCESSING;
                        $document->save();
                        
                        $descriptor = $document->fresh();
                        
                        // Considering a new file version as uploading a new file
                        if ($uploaded_new_version) {
                            event(new UploadCompleted($descriptor, $descriptor->owner));
                        } else {
                            dispatch(new ReindexDocument($descriptor, KlinkVisibilityType::KLINK_PRIVATE));
                        }

                        if ($descriptor->isPublished()) {
                            // trigger update also for eventual publications,
                            // as the public document must be in sync with the local copy
                            dispatch(new UpdatePublishedDocumentJob($descriptor));
                        }
                    }
                } else {
                    $document->touch();
                }
                
                return $document;
            });

            if ($request->wantsJson()) {
                return new JsonResponse($ret, 200);
            }

            return redirect()->route('documents.edit', $id)->with([
                'flash_message' => trans('documents.messages.updated')
            ]);
        } catch (\Exception $kex) {
            \Log::error('Document updating error', ['error' => $kex, 'id' => $id]);

            $status = ['status' => 'error', 'message' =>  trans('documents.update.error', ['error' => $kex->getMessage()])];

            if ($request->wantsJson()) {
                return new JsonResponse($status, 500);
            }

            return redirect()->route('documents.edit', $id)->withInput()->withErrors([
                'error' => trans('documents.update.error', ['error' => $kex->getMessage()])
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(AuthGuard $auth, Request $request, $id)
    {
        try {
            $user = $auth->user();
            
            if (! $user->can_capability(Capability::DELETE_DOCUMENT)) {
                throw new ForbiddenException(trans('documents.messages.delete_forbidden'), 1);
            }
            
            $descriptor = DocumentDescriptor::withTrashed()->findOrFail($id);

            // TODO: if is a reference to a public document remove it if is not starred, shared or in a collection
            
            if ($descriptor->isPublic() && ! $user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY)) {
                \Log::warning('User tried to delete a public document without permission', ['user' => $user->id, 'document' => $id]);
                throw new ForbiddenException(trans('documents.messages.delete_public_forbidden'), 2);
            }
            
            $force = $request->input('force', false);

            if ($force && ! $user->can_capability(Capability::CLEAN_TRASH)) {
                \Log::warning('User tried to force delete a document without permission', ['user' => $user->id, 'document' => $id]);
                throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
            }
    
            \Log::info('Deleting Document', ['params' => $id]);
    
            if (! $force || $force && ! $descriptor->trashed()) {
                $this->service->deleteDocument($user, $descriptor);
            } else {
                $this->service->permanentlyDeleteDocument($user, $descriptor);
            }

            if ($request->wantsJson()) {
                return new JsonResponse(['status' => 'ok', 'message' => $force ? trans('documents.permanent_delete.deleted_dialog_title', ['document' => $descriptor->title]): trans('documents.delete.deleted_dialog_title', ['document' => $descriptor->title])], 202);
            }

            return response('ok', 202);
        } catch (\Exception $kex) {
            \Log::error('Document deleting error', ['error' => $kex, 'id' => $id]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.remove_error', ['error' => $kex->getMessage()])];

            if ($request->wantsJson()) {
                return new JsonResponse($status, 422);
            }

            return response('error');
        }
    }
}
