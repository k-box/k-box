<?php

namespace KBox\Http\Controllers;

use Illuminate\Http\Request;
use KBox\DocumentDescriptor;
use KBox\Group;
use KBox\PeopleGroup;
use KBox\Capability;
use KBox\Shared;
use KBox\User;
use KBox\Project;
use Illuminate\Http\JsonResponse;
use KBox\Http\Requests\CreateShareRequest;
use KBox\Http\Requests\ShareDialogRequest;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Support\Collection;
use KBox\Traits\Searchable;
use KBox\Events\ShareCreated;

/**
 * Manage you personal shares
 */
class SharingController extends Controller
{
    use Searchable;

    /**
     * [$adapter description]
     * @var Klink\DmsDocuments\DocumentsService
     */
    private $service = null;
    
    /**
     * [$adapter description]
     * @var Klink\DmsAdapter\Contracts\KlinkAdapter
     */
    private $adapter = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService, \Klink\DmsAdapter\Contracts\KlinkAdapter $adapter)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->service = $adapterService;
        
        $this->adapter = $adapter;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(AuthGuard $auth, Request $request)
    {
        $user = $auth->user();
        
// 		$group_ids = $user->involvedingroups()->get(array('peoplegroup_id'))->pluck('peoplegroup_id')->toArray();
//
// 		$all_in_groups = Shared::sharedWithGroups($group_ids)->get();
//
// 		$all_single = Shared::sharedWithMe($user)->with(array('shareable', 'sharedwith'))->get();
//
// 		$all = $all_single->merge($all_in_groups)->unique();
        
        $order = $request->input('o', 'd') === 'a' ? 'ASC' : 'DESC';
        
        $req = $this->searchRequestCreate($request);
        
        $req->visibility('private');
        
        $all = $this->search($req, function ($_request) use ($user, $order) {
            $group_ids = $user->involvedingroups()->get(['peoplegroup_id'])->pluck('peoplegroup_id')->toArray();
                    
            $all_in_groups = Shared::sharedWithGroups($group_ids)->orderBy('created_at', $order)->get();
            
                
            $all_single = Shared::sharedWithMe($user)->orderBy('created_at', $order)->with(['shareable', 'sharedwith'])->get();
            
            $all_shared = $all_single->merge($all_in_groups)->unique();
            
            $shared_docs = $all_shared->pluck('shareable.uuid')->all();
            $shared_files_in_groups = array_flatten(array_filter($all_shared->map(function ($g) {
                if ($g->shareable_type === 'KBox\Group' && ! is_null($g->shareable)) {
                    return $g->shareable->documents->pluck('uuid')->all();
                }
                return null;
            })->all()));
            
            $_request->in(array_merge($shared_docs, $shared_files_in_groups));
            
            if ($_request->isPageRequested()) {
                $_request->setForceFacetsRequest();
                
                return $all_shared;
            }
            
            return false; // force to execute a search on the core instead on the database
        });
        

        return view('share.list', [
            'shares' => $all,
            'pagetitle' => trans('share.page_title'),
            'context' => 'sharing',
            'current_visibility' => 'private',
            'pagination' => $all,
            'order' => $order,
            'search_terms' => $req->term,
            'facets' => $all->facets(),
            'filters' => $all->filters(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id -> TOKEN
     * @return Response
     */
    public function show(AuthGuard $auth, Request $request, $id)
    {
        $share = null;

        if (is_int($id)) {
            $share = Shared::findOrFail($id);
        } else {
            $share = Shared::token($id)->first();
        }

        if (is_null($share)) {
            throw (new ModelNotFoundException)->setModel('KBox/Shared');
        }

        $share = $share->load('shareable');

        if ($request->wantsJson()) {
            return new JsonResponse($share->toArray(), 200);
        }

        $see_share = $auth->user()->can_capability(Capability::RECEIVE_AND_SEE_SHARE);
        $partner = $auth->user()->can_all_capabilities(Capability::$PARTNER);

        if (is_a($share->shareable, 'KBox\Group')) {
            return redirect()->route($partner ? 'documents.groups.show' : 'shares.group', ['id' => $share->shareable->id]);
        }

        return redirect()->route($partner ? 'documents.sharedwithme' : 'shares.index', ['highlight' => $share->shareable->id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AuthGuard $auth, ShareDialogRequest $request)
    {
        $me = $auth->user();

        $groups_input = $request->input('collections', []);
        $documents_input = $request->input('documents', []);

        $groups_req = is_array($groups_input) ? $groups_input : array_filter(explode(',', $request->input('collections', '')));

        $documents_req = is_array($documents_input) ? $documents_input : array_filter(explode(',', $request->input('documents', '')));

        $documents = DocumentDescriptor::whereIn('id', $documents_req)->get();

        $groups = Group::whereIn('id', $groups_req)->get();

        $all_in = $documents->merge($groups);
        
        $first = $all_in->first();

        $elements_count = $all_in->count();
        $is_multiple_selection = $elements_count > 1;

        // details for public/private
        $is_public = false;

        $existing_shares = null;

        // users to exclude from the available for share
        $users_to_exclude = [$me->id];

        $public_link = null;
        $has_publishing_request = false;
        $publication = null;

        if (! is_null($first) && $first instanceof DocumentDescriptor && ! $is_multiple_selection) {
            $is_public = $first->isPublished();
            $has_publishing_request = $first->hasPendingPublications();
            $publication = $first->publication();
            
            // grab the existing share made by the user, so we can remove it also from the available_users
            // let's do it for $first only first

            $existing_shares = $first->shares()->sharedByMe($me)->where('sharedwith_type', 'KBox\User')->get();

            $users_to_exclude = array_merge($users_to_exclude, $existing_shares->pluck('sharedwith_id')->unique()->toArray());

            // is the document in a project? the current user has access to the project? if yes we can also remove the members of that project(s)
            $users_from_projects = $this->service->getUsersWithAccess($first, $me);
            //  $first->projects()->map(function($p) use($me){
                
            // 	if(!Project::isAccessibleBy($p, $me)){
            // 		return false;
            // 	}
                
            // 	$users =  $p->users()->get();

            // 	if($p->manager->id != $me->id){
            // 		$users = $users->merge([$p->manager]);
            // 	}

            // 	return $users;

            // })->flatten();

            $users_to_exclude = array_merge($users_to_exclude, $users_from_projects->pluck('id')->toArray());
            $existing_shares = $existing_shares->merge($users_from_projects);

            // is the document in a shared collection? if yes a user could still have access to the document because of that

            if ($first->hasPublicLink()) {
                $public_link_share = $first->shares()->where('sharedwith_type', 'KBox\PublicLink')->first();
                $public_link = $public_link_share->sharedwith; //instance of PublicLink
                $existing_shares = $existing_shares->merge([$public_link_share]);
            }
        } elseif (! is_null($first) && $first instanceof Group && ! $is_multiple_selection) {
            
            // grab the existing share made by the user, so we can remove it also from the available_users
            // let's do it for $first only first

            $existing_shares = $first->shares()->sharedByMe($me)->where('sharedwith_type', 'KBox\User')->get();

            $users_to_exclude = array_merge($users_to_exclude, $existing_shares->pluck('sharedwith_id')->unique()->toArray());
        }

        $available_users = User::whereNotIn('id', $users_to_exclude)->whereHas('capabilities', function ($q) {
            $q->where('key', '=', Capability::RECEIVE_AND_SEE_SHARE);
        })->get();
        
        
        $can_share = $me->can_capability(Capability::SHARE_WITH_PRIVATE) || $me->can_capability(Capability::SHARE_WITH_PERSONAL);
        $can_make_public = $me->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY);
        $is_project_manager = $me->isProjectManager();

        // TODO: check if the document is in a personal collection only, or in a project collection
        $sharing_links = [];

        $all_in->each(function ($item) use (&$sharing_links) {
            $real_preview_link = null;
            
            if (! is_null($item) && ! $item instanceof Group) {
                $real_preview_link = \DmsRouting::preview($item);

                if (! $item->isRemoteWebPage() && starts_with($item->document_uri, 'http://msri-hub.ucentralasia.org/') || starts_with($item->document_uri, 'http://staging-uca.cloudapp.net/')) {
                    $real_preview_link = $item->document_uri;
                } elseif ($item->isRemoteWebPage() && ! is_null($item->file)) {
                    $real_preview_link = $item->file->original_uri;
                }
            } elseif (! is_null($item) && $item instanceof Group) {
                $real_preview_link = route('documents.groups.show', $item->id);
            }

            $sharing_links[] = $real_preview_link;
        });

        return view('share.dialog', [
            'is_network_enabled' => network_enabled(),
            'existing_shares' => $existing_shares,
            'can_make_public' => $can_make_public,
            'users' => $available_users,
            'sharing_links' => implode('&#13;&#10;', $sharing_links),
            'public_link' => $public_link,
            'documents' => $documents,
            'groups' => $groups,
            'has_documents' => count($documents_req) > 0,
            'has_groups' => count($groups_req) > 0,
            'panel_title' => $elements_count == 1 ? trans('share.dialog.subtitle_single', ['what' => $first instanceof Group ? $first->name : $first->title]) : trans_choice('share.dialog.subtitle_multiple', $elements_count-1, ['count' => $elements_count-1, 'what' => $first instanceof Group ? $first->name : $first->title]),
            'elements_count' => $elements_count,
            'is_multiple_selection' => $is_multiple_selection,
            'is_public' => $is_public,
            'has_publishing_request' => $has_publishing_request,
            'publication' => $publication,
            'publication_status' => $publication ? $publication->status : null,
            'is_collection' => $first instanceof Group,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(AuthGuard $auth, CreateShareRequest $request)
    {

        // with_users
        // with_people
        // groups
        // documents

        $status = \DB::transaction(function () use ($auth, $request) {
            $user = $auth->user();

            $users_to_share_with = $request->input('with_users', []);
            $people_to_share_with = $request->input('with_people', []);

            $groups = $request->has('groups') ? $request->input('groups') : [];

            $documents = $request->has('documents') ? $request->input('documents') : [];

            $groups_to_share = Group::whereIn('id', $groups)->get();
            
            $documents_to_share = DocumentDescriptor::whereIn('id', $documents)->get();
            
            $user_dest = User::whereIn('id', $users_to_share_with)->get();
            
            $people_dest = PeopleGroup::whereIn('id', $people_to_share_with)->get();
            
            $shares_list = $this->createShare($groups_to_share->merge($documents_to_share), $user_dest->merge($people_dest), $user);

            
            return ['status' => 'ok', 'message' => trans_choice('share.share_created_msg', $shares_list->count(), ['num' => $shares_list->count()])];
        });

        if ($request->wantsJson()) {
            return new JsonResponse($status, 201);
        }

        return view('panels.share_done', $status);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AuthGuard $auth, $id)
    {
        // Share edit panel??!?!??!
        //
        return view('share.create', ['edit' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(AuthGuard $auth, CreateShareRequest $request, $id)
    {
        $share = Shared::findOrFail($id);
    }
    
    
    private function _destroy($id)
    {
        $share = Shared::findOrFail($id);

        $share->delete();
        
        //TODO: send email to the user to tell him that the sare is no more available
        //TODO: send a notification to the user that the share is no more available
        //TODO: event for share removed ??!?!?!?!?
        
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(AuthGuard $auth, Request $request, $id)
    {
        $executed = $this->_destroy($id);
        
        $status = ['status' => 'ok', 'message' => trans('share.removed')];
        
        if ($request->wantsJson()) {
            return new JsonResponse($status, 200);
        }

        return response($status);
    }
    
    
    public function deleteMultiple(AuthGuard $auth, Request $request)
    {
        $ids = $request->input('shares', []);
        
        if (empty($ids)) {
            response(['status' => 'ok']);
        }
        
        $errors = [];
        
        foreach ($ids as $id) {
            try {
                $executed = $this->_destroy($id);
            } catch (\Exception $ex) {
                \Log::error('Sharing Destroy error', ['error' => $kex, 'share_id' => $id, 'request' => $request]);
                $errors[] = $ex->getMessage();
            }
        }
        
        $status = ['status' => ! empty($errors) ? 'errors' : 'ok', 'message' => trans_choice('share.bulk_destroy', count($errors), ['errors' => implode(',', $errors)])];
        
        if ($request->wantsJson()) {
            return new JsonResponse($status, 200);
        }

        return response($status);
    }
    
    /**
     * Really create a share
     *
     * @param $what Group||Descriptor
     * @param $with User||PeopleGroup
    */
    private function createShare(Collection $what, Collection $with, User $by)
    {
        $token_content = '.';
        
        $shares_list = new Collection;
        $single_share = null;

        foreach ($with as $target) {
            foreach ($what as $object) {
                $token_content = $by->id.$target->id.get_class($target).time().$object->id.get_class($object);

                if (! Shared::byWithWhat($by, $target, $object)->exists()) {
                    $single_share = $object->shares()->create([
                            'user_id' => $by->id,
                            'sharedwith_id' => $target->id, //the id
                            'sharedwith_type' => get_class($target), //the class
                            'token' => hash('sha256', $token_content),
                        ]);

                    event(new ShareCreated($single_share));
                        
                    $shares_list->push($single_share);
                }
            }
        }
            

        return $shares_list;
            // TODO: now send a notification/mail to every user about their new shares
    }
    
    
    
    public function showGroup(AuthGuard $auth, Request $request, $id)
    {
        
                
        // if shareable == group, Search is possible
        
        $group = Group::findOrFail($id);
        
        // $all = $group->documents()->get();
        
        $req = $this->searchRequestCreate($request);
        
        $req->visibility('private');
        
        $all = $this->search($req, function ($_request) use ($group) {
            $group_ids = [$group->toKlinkGroup()];
            
            $group_ids = array_merge($group_ids, $group->getDescendants()->map(function ($grp) {
                return $grp->toKlinkGroup();
            })->all());
            
            $_request->on($group_ids);
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $_request->setForceFacetsRequest();

                return $group->documents()->get();
            }
            
            return false;
        }, function ($res_item) {
            return DocumentDescriptor::where('local_document_id', $res_item->localDocumentID)->first();
        });
        
        

        return view('share.list', [
            'shares' => $all,
            'pagetitle' => $group->name.' - '.trans('share.page_title'),
            'shared_group' => $group,
            'context' => 'sharing',
            'current_visibility' => 'private',
            'pagination' => $all,
            'search_terms' => $req->term,
            'facets' => $all->facets(),
            'filters' => $all->filters(),
        ]);
    }
}
