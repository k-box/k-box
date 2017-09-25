<?php

namespace KlinkDMS\Http\Controllers\Document;

use Illuminate\Http\Request;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Shared;
use KlinkDMS\Group;
use KlinkDMS\Capability;
use KlinkDMS\Project;
use KlinkDMS\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KlinkDMS\Http\Requests\DocumentAddRequest;
use KlinkDMS\Http\Requests\DocumentUpdateRequest;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use KlinkDMS\Exceptions\FileNamingException;
use KlinkDMS\Exceptions\ForbiddenException;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KlinkDMS\Traits\Searchable;
use KlinkDMS\Events\UploadCompleted;

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
    
    // private $searchService = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService/*, \Klink\DmsSearch\SearchService $searchService*/)
    {
        $this->middleware('auth', ['except' => ['showByKlinkId']]);

        $this->middleware('capabilities', ['except' => ['showByKlinkId']]);

        $this->service = $adapterService;
        
        // $this->searchService = $searchService;
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
            if ($_request->visibility === \KlinkVisibilityType::KLINK_PUBLIC) {
                // if public => return direct search because we want them to see the public network
                return false;
            }
            
            if ($is_personal) {
                $personal_doc_id = DocumentDescriptor::local()->private()->ofUser($user->id)->get(['local_document_id'])->pluck('local_document_id')->all();
                
                $_request->in($personal_doc_id);
            }
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = DocumentDescriptor::local();
                
                $_request->setForceFacetsRequest();
            
                if ($_request->visibility === \KlinkVisibilityType::KLINK_PRIVATE) {
                    $all_query = $all_query->private();
                    if ($is_personal) {
                        $all_query = $all_query->ofUser($user->id);
                    }
                }
                
                
                
                return $all_query->orderBy('title', 'ASC');
            }
            
            
            
            
            return false; // force to execute a search on the core instead on the database
        }, function ($res_item) {
            $local = DocumentDescriptor::where('local_document_id', $res_item->getLocalDocumentID())->first();
            return ! is_null($local) ? $local : $res_item;
        });

        // Adding user's root groups and institution level groups to the result
        // $groups = Group::roots()->private($auth->user()->id)->orPublic()->get();

        return view('documents.documents', [
            'pagetitle' => (is_null($visibility) ? '': ($visibility === 'public' ? network_name().' ' : trans('documents.menu.'.($is_personal ? 'personal' : $visibility)).' ')).trans('documents.page_title'),
            'documents' => $results->getCollection(), /*'collections' => $groups,*/
            'context' => is_null($visibility) ? 'all' : $visibility,
            'pagination' => $results,
            'search_terms' => $req->term,
            'facets' => $results->facets(),
            'filters' => $results->filters(),
            'current_visibility' => $is_personal ? 'private' : $visibility,
            'is_personal' => $is_personal,
            'hint' => $showing_only_local_public ? trans('documents.messages.local_public_only') : false,
            'filter' => $visibility === 'public' ? network_name() : trans('documents.menu.'.($is_personal ? 'personal' : $visibility))
            ]);
    }
    
    public function recent(AuthGuard $auth, Request $request, $range = 'currentweek')
    {
        $base_now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $init_of_month = $base_now->copy()->startOfMonth();
        $init_of_month_diff = $base_now->copy()->startOfMonth()->diffInDays($today);
        $start_of_week = $today->copy()->previous(Carbon::MONDAY);
        $last_7_days = $base_now->copy()->subDays(7);
        $last_30_days = $today->copy()->subMonth();

        $user = $auth->user();
        
        $user_is_dms_manager = $user->isDMSManager();
        
        $items_per_page = $user->optionItemsPerPage();

        $requested_items_per_page = (int)$request->input('n', $items_per_page);
        
        $order = $request->input('o', 'd') === 'a' ? 'ASC' : 'DESC';

        try {
            if ($items_per_page !== $requested_items_per_page) {
                $user->setOptionItemsPerPage($requested_items_per_page);
                $items_per_page = $requested_items_per_page;
            }
        } catch (\Exception $limit_ex) {
        }

        // future proof for when this option will be saved in the user profile
        $selected_range = $user->optionRecentRange();

        if ($selected_range !== $range) {
            $selected_range = $range;
            $user->setOption(User::OPTION_RECENT_RANGE, $range);
        }

        $req = $this->searchRequestCreate($request);
        
        $req->limit($items_per_page);
        
        // Last Private Documents
        
        $documents_query = DocumentDescriptor::local()->private()->take(config('dms.recent.limit'));
        
        if (! $user_is_dms_manager) {
            $documents_query = $documents_query->ofUser($user->id);
        }
        
        // last shared documents from other users
        $shared_query = Shared::sharedWithMe($user)->take(config('dms.recent.limit'));

        // documents that have been updated in a project that the user has access to
        $all_projects_with_documents_query = $user->projects()->orWhere('projects.user_id', $user->id)->with('collection.documents');

        $shared_table = with(new Shared)->getTable();
        $descriptor_table = with(new DocumentDescriptor)->getTable();
        $shared_updated_at_field = $shared_table.'.updated_at';

        // limit all queries to the maximum number of documents take( config('dms.recent.limit') )

        if ($selected_range === 'today') {
            $documents_query = $documents_query->where('updated_at', '>=', $today);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $today);

            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($today, $order) {
                $query->where('document_descriptors.updated_at', '>=', $today)
                              ->orderBy('updated_at', $order);
            });
        } elseif ($selected_range === 'yesterday') {
            $documents_query = $documents_query->where('updated_at', '>=', $yesterday);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $yesterday);

            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($yesterday, $order) {
                $query->where('document_descriptors.updated_at', '>=', $yesterday)
                              ->orderBy('updated_at', $order);
            });
        } elseif ($selected_range === 'currentweek') {
            $documents_query = $documents_query->where('updated_at', '>=', $last_7_days);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $last_7_days);

            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($last_7_days, $order) {
                $query->where('document_descriptors.updated_at', '>=', $last_7_days)
                              ->orderBy('updated_at', $order);
            });
        } elseif ($selected_range === 'currentmonth') {
            $documents_query = $documents_query->where('updated_at', '>=', $last_30_days);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $last_30_days);
            
            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($last_30_days, $order) {
                $query->where('document_descriptors.updated_at', '>=', $last_30_days)
                              ->orderBy('updated_at', $order);
            });
        }

        $documents_query = $documents_query->orderBy('updated_at', $order)->get(['id', 'local_document_id', 'updated_at']);
        // $documents_query = $documents_query->orderBy('updated_at', $order)->get(['id', 'local_document_id', 'updated_at']);
        
        $shared_query = $shared_query->orderBy('updated_at', $order)->
                where('shareable_type', '=', 'KlinkDMS\DocumentDescriptor')
                ->join('document_descriptors', 'shareable_id', '=', 'document_descriptors.id')
                ->get([$descriptor_table.'.id', // this for having the descriptor ID in the id field
                       $shared_updated_at_field,
                       $descriptor_table.'.local_document_id']);

        // let's make them together
        $list_of_docs = $documents_query->merge($shared_query);

        if (! $user_is_dms_manager) {
            // add the projects only if is not a DMS admin, otherwise only duplicates will be added
            $all_projects_with_documents = $all_projects_with_documents_query->get()->map(function ($e) {
                return $e->collection->documents;
            })->collapse()->map(function ($e) {
                $internal = new DocumentDescriptor([
                    'id' => $e->id,
                    'local_document_id' => $e->local_document_id,
                    'updated_at' => $e->updated_at,
                ]);
                $internal->id = $e->id;

                return $internal;
            });
            
            $list_of_docs = $list_of_docs->merge($all_projects_with_documents);
        }

        // sort all the ids, remove duplicates and take the maximum amount
        $list_of_docs = $list_of_docs->unique(function ($u) {
            return $u->id;
        });
        if ($list_of_docs->count() > config('dms.recent.limit')) {
            $list_of_docs = $list_of_docs->take(config('dms.recent.limit'))
                ->sort(function ($el) {
                    return $el->updated_at->timestamp;
                });
        }

        
        // get the id of the last (bla bla bla) and group them
        
        $req->visibility('private');
        
        $results = $this->search($req, function ($_request) use ($user, $list_of_docs, $order) {
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = DocumentDescriptor::whereIn('id', $list_of_docs->pluck('id')->all());
                
                return $all_query->orderBy('updated_at', $order); //ASC or DESC
            }
            
            $_request->in($list_of_docs->pluck('local_document_id')->all());
            
            return false; // force to execute a search on the core instead on the database
        }, function ($res_item) {
            $local = DocumentDescriptor::where('local_document_id', $res_item->getLocalDocumentID())->first();
            return ! is_null($local) ? $local : $res_item;
        });
        
        
        
        $grouped = $results->getCollection()->groupBy(function ($date) use ($start_of_week, $init_of_month, $init_of_month_diff) {
            if ($date->updated_at->isToday()) {
                $group = trans('units.today');
            } elseif ($date->updated_at->isYesterday()) {
                $group = trans('units.yesterday');
            } elseif ($date->updated_at->diffInDays($start_of_week) <= 6) {
                $group = trans('units.this_week');
            } elseif ($date->updated_at->diffInDays($init_of_month) < $init_of_month_diff-1) {
                $group = trans('units.this_month');
            } else {
                $group = trans('units.older');
            }
            
            return $group;
        });

        return view('documents.recent', [
            'search_terms' => $req->term,
            'is_search_requested' => $req->isSearchRequested(),
            'search_replica_parameters' => $request->only('s'),
            'pagination' => $results,
            'range' => $selected_range,
            'order' => $order,
            'info_message' => $user_is_dms_manager ? trans('documents.messages.recent_hint_dms_manager') : null,
            'list_style_current' => $user->optionListStyle(),
            'pagetitle' => trans('documents.menu.recent').' '.trans('documents.page_title'),
            'documents' => $grouped,
            'groupings' => array_keys($grouped->toArray()),
            'context' => 'recent',
            'filter' => trans('documents.menu.recent')]);
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
            
            $shared_docs = $all_shared->pluck('shareable.local_document_id')->all();
            
            $_request->in($shared_docs);
            
            if ($_request->isPageRequested()) {
                $_request->setForceFacetsRequest();
                
                return $all_shared;
            }
            
            
            
            return false; // force to execute a search on the core instead on the database
        }, function ($res_item) {
            // from KlinkSearchResultItem to Shared instance
            return DocumentDescriptor::where('local_document_id', $res_item->localDocumentID)->first();
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
        } catch (FileAlreadyExistsException $ex) {
            \Log::warning('DocumentsController store - File already exists check', ['error' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $ex->render($auth->user())], 409);
            }
            
            return redirect()->back()->withErrors([
                'error' => $ex->render($auth->user())
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
                $existing_shares = $document->shares()->sharedByMe($auth_user)->where('sharedwith_type', 'KlinkDMS\User')->count();
                $public_link_shares = $document->shares()->where('sharedwith_type', 'KlinkDMS\PublicLink')->count();
                
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
    public function show($id, AuthGuard $auth)
    {
        try {
            $document = DocumentDescriptor::withTrashed()->findOrFail($id);

            if (\Request::ajax()) {
                return $this->_showPanel($document, $auth->user());
            }

            $url = \KlinkDMS\RoutingHelpers::preview($document);

            return redirect($url);
        } catch (ModelNotFoundException $kex) {
            \Log::warning('Document Descriptor not found', ['error' => $kex, 'id' => $id]);
            return view('panels.error', ['error_title' => trans('errors.404_title'), 'message' => $kex->getMessage()]);
        } catch (ForbiddenException $kex) {
            \Log::warning('Document Descriptor not accessible by user', ['error' => $kex, 'id' => $id, 'user' => $auth->user()->id]);
            
            return view('panels.error', ['error_title' => trans('errors.403_title'), 'message' => trans('errors.forbidden_see_document_exception')]);
        } catch (\Exception $kex) {
            \Log::error('Document Descriptor panel show error', ['error' => $kex, 'id' => $id]);
            return view('panels.error', ['message' => $kex->getMessage()]);
        }
    }

    public function showByKlinkId($institution, $local_id, AuthGuard $auth)
    {
        try {
            $document = $this->service->getDocument($institution, $local_id);

            return $this->_showPanel($document, $auth()->user());
        } catch (\KlinkException $kex) {
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
                    'versions' => ! is_null($document->file) ? $document->file->revisionOfRecursive()->get() : new Collection,
                    'pagetitle' => trans('documents.edit.page_title', ['document' => $document->title]),
                    'context' => 'document', 'context_document' => $document->id, 'filter' => $document->name,
                ];

            return view('documents.edit', $view_params);
        } catch (ForbiddenException $kex) {
            \Log::warning('User tried to edit a document who don\'t has access to', ['error' => $kex, 'user' => $auth->user()->id, 'document' => $id]);
            
            throw $kex;
        } catch (\Exception $kex) {
            \Log::error('Error generating data for documents.edit view', ['error' => $kex]);
            
            return view('panels.error', ['message' => $kex->getMessage()]);
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

                $was_document_public = $document->is_public;
                $is_json = $request->isJson();
                $has_visibility =$request->has('visibility');
                 
                if ($user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY)) {
                    if ($request->has('visibility') && $request->input('visibility') === \KlinkVisibilityType::KLINK_PUBLIC && ! $was_document_public) {
                        // if was not public and is marked as public
                        $document->is_public = true;
                        
                        \Log::info('Document should be added to public', ['descriptor' => $document->id, 'triggered_by' => $user->id]);
                    } elseif ($request->has('visibility') && $request->input('visibility') === \KlinkVisibilityType::KLINK_PRIVATE && $was_document_public) {
                        //was public and is no more marker as public
                        $document->is_public = false;

                        \Log::info('Document should be removed from public', ['descriptor' => $document->id, 'triggered_by' => $user->id]);
                    } elseif (! $request->wantsJson() && ! $request->has('visibility') && $was_document_public) {
                        //was public and is no more marker as public
                        $document->is_public = false;

                        \Log::info('Document should be removed from public', ['descriptor' => $document->id, 'triggered_by' => $user->id, 'comes_from' => 'documents.edit']);
                    }
                }

                if ($request->has('authors') && $request->input('authors') !== $document->authors) {
                    $document->authors = e($request->input('authors')); //deve essere un array così poi laravel lo serializza
                }

                // handle new file version
            
                try {
                    if ($request->hasFile('document') && $request->file('document')->isValid()) {
                        \Log::info('Update Document with new version');
        

                        //test and report exceptions
                        $file_model = $this->service->createFileFromUpload($request->file('document'), $user, $document->file);

                        $document->file_id = $file_model->id;
                        $document->mime_type = $file_model->mime_type;
                        $document->document_type = \KlinkDocumentUtils::documentTypeFromMimeType($file_model->mime_type);
                        $document->hash = \KlinkDocumentUtils::generateDocumentHash($file_model->path);
                    } elseif ($request->hasFile('document')) {
                        throw new Exception(trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()]), 400);
                    }
                } catch (FileAlreadyExistsException $fex) {
                    throw new Exception($fex->render($user));
                }

                // save everything if the descriptor isDirty and do the reindex if necesary
                
                if ($document->isDirty() || $group_dirty) {
                    $document->save();

                    if ($document->isFileUploadComplete()) {
                        $this->service->reindexDocument($document, \KlinkVisibilityType::KLINK_PRIVATE);
                        
                        if ($user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY)) {
                            if (! $was_document_public && $document->is_public) {
                                \Log::info('Applying visibility change', ['descriptor' => $document->id, 'old' => $was_document_public, 'new' => $document->is_public]);
                                $this->service->reindexDocument($document, \KlinkVisibilityType::KLINK_PUBLIC);
                            } elseif ($was_document_public && ! $document->is_public) {
                                \Log::info('Applying visibility change', ['descriptor' => $document->id, 'old' => $was_document_public, 'new' => $document->is_public]);
                                $this->service->deletePublicDocument($document);
                            } elseif ($was_document_public && $document->is_public) {
                                \Log::info('Reindexing also Public Document because Doc is dirty', ['descriptor' => $document->id]);
                                $this->service->reindexDocument($document, \KlinkVisibilityType::KLINK_PUBLIC);
                            }
                        } elseif ($was_document_public && $document->is_public) {
                            \Log::info('Reindexing also Public Document because Doc is dirty', ['descriptor' => $document->id]);
                            $this->service->reindexDocument($document, \KlinkVisibilityType::KLINK_PUBLIC);
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
