<?php

namespace KBox\Http\Controllers\Document;

use KBox\Group;
use KBox\Http\Controllers\Controller;
use KBox\Capability;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KBox\Http\Requests\CreateGroupRequest;
use KBox\Http\Requests\UpdateGroupRequest;
use Illuminate\Support\Collection;
use KBox\Exceptions\GroupAlreadyExistsException;
use KBox\Exceptions\CollectionMoveException;
use Illuminate\Http\Request;
use KBox\Traits\Searchable;
use KBox\Exceptions\ForbiddenException;

class GroupsController extends Controller
{
    use Searchable;

    /**
     * [$adapter description]
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\KBox\Documents\Services\DocumentsService $service)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->service = $service;
    }

    private function echoTree($tree)
    {
        echo '<pre>';

        foreach ($tree as $first_level) {
            echo '|- '.$first_level->name.'<br/>';

            if ($first_level->hasChildren()) {
                foreach ($first_level->getChildren() as $second_level) {
                    echo '|  |- '.$second_level->name.'<br/>';

                    if ($second_level->hasChildren()) {
                        foreach ($second_level->getChildren() as $third_level) {
                            echo '|  |  |- '.$third_level->name.'<br/>';
                        }
                    }
                }
            }
            echo '|<br/>';
        }

        echo '</pre>';
    }

    /**
     * Display a listing of groups.
     *
     * @return Response
     */
    public function index(AuthGuard $auth, Request $request)
    {

        // ok famo una prova creiamo dei gruppi e associamoli a un doc e vediamo che succede

        $user = $auth->user();

        if ($user->isContentManager()) {
            $all = Group::getTree();
        } else {
            $all = Group::roots()->private($user->id)->get();
        }
        
        $private_groups = \Cache::remember('dms_institution_collections', 60, function () {
            return Group::getTreeWhere('is_private', '=', false);
        });
        
        $personal_groups = \Cache::remember('dms_personal_collections'.$user->id, 60, function () use ($user) {
            return Group::getPersonalTree($user->id);
        });
        
        if ($request->ajax() && $request->wantsJson()) {
            return new JsonResponse(compact('private_groups', 'personal_groups'), 200);
        }

        return view('groups.manage', [
            'pagetitle' => trans('groups.collections.title'),
            'context' => 'groups-manage',
            'filter' => trans('groups.collections.title'),
            'groups' => $all,
            'groups_count' => $all->count(),
            'empty_message' => trans('groups.empty_msg')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AuthGuard $auth, Request $request)
    {
        $auth_user = $auth->user();
        
        $view_args = [
            'show_cancel' => true, 'create' => true,
            'title' => trans('groups.panel_create_title'),
            'main_action' => trans('groups.create_btn')];

        // if context info is available
        
        $is_private = $request->input('isPrivate', true)  === "false" || $request->input('isPrivate', true)  === false  ? false: true;
        $view_args['private'] = $is_private;
        
        // if($is_private){
        // 	$view_args['collections'] = Group::getPersonalTree($auth_user->id);
        // }
        // else {
        // 	$view_args['collections'] = Group::getTreeWhere('is_private', '=', false);
        // }
        
        if ($request->has('group_context')) {
            // preselect a parent collection

            $group = Group::findOrFail($request->input('group_context', 0));

            $view_args = array_merge($view_args, [
                'show_parent' => true,
                'parent_label' => $group->name,
                'parent_id' => $group->id,
                'is_public_collection' => ! $group->is_private
            ]);
        } else {
        }

        return view('panels.group', $view_args);
    }

    /**
     * Store a newly created group in storage.
     *
     * @return Response
     */
    public function store(AuthGuard $auth, CreateGroupRequest $request)
    {
        try {
            $group_name = e($request->input('name'));

            $color = $request->input('color', null);

            $public = $request->input('public', false);

            $parent = $request->input('parent', null);

            $parent_group = is_null($parent) ? $parent : Group::findOrFail($parent);

            $created_group = $this->service->createGroup($auth->user(), $group_name, $color, $parent_group, ! $public);

            \Cache::flush();

            if ($request->ajax() && $request->wantsJson()) {
                // return response()->json($created_group);
                return new JsonResponse($created_group->toArray(), 201);
            } elseif ($request->ajax() && $request->input('ok_template', false)) {
                return view('groups.tree');
            }

            return response('created', 201);
        } catch (ForbiddenException $fe) {
            //return forbidden response

            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 403);
            }

            return response("forbidden", 403);
        } catch (GroupAlreadyExistsException $fe) {
            if ($request->ajax() && $request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 409);
            }

            return response($fe->getMessage(), 409);
        }
    }

    /**
     * Display the specified group.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(AuthGuard $auth, Request $request, $id)
    {
        $req = $this->searchRequestCreate($request);
        
        $req->visibility('private');
        
        $user = $auth->user();
        
        $group = Group::findOrFail($id);
        
        if (! $this->service->isCollectionAccessible($user, $group)) {
            throw new ForbiddenException(trans('errors.401_title'), 401);
        }
        // getCollectionsAccessibleByUser
        
        // GET the current $group and all sub-collections accessible by the User
        // Could be the case that a sub-collection is marked private?
        $group_ids = [$group->toKlinkGroup()];

        $group_ids = array_merge($group_ids, $this->service->getCollectionsAccessibleByUserFrom($user, $group)->map(function ($grp) {
            return $grp->toKlinkGroup();
        })->all());
        
        $results = $this->search($req, function ($_request) use ($user, $group, $group_ids) {
            $_request->on($group_ids);
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $_request->setForceFacetsRequest();

                return $group->documents;
            }
            
            // get all sub-collections for making the correct search request -> on(...)
            
            return false;
        });

        // The the ancestors that are viewable by the current user
        $parents = $group->getAncestors()->reverse()->filter(function ($c) use ($user) {
            return $this->service->isCollectionAccessible($user, $c);
        });

        // coming from shared section if collection is explicitely shared with the current user
        $browsing_via_shared = $group->isSharedWith($user);

        return view('documents.documents', [
            'pagetitle' => $group->name,
            'documents' => $results,
            'collections' => [],
            'context' => 'group',
            'context_group' => $group->id,
            'context_group_instance' => $group,
            'context_group_shared' => $browsing_via_shared,
            'filter' => $group->name,
            'parents' => $parents,
            'pagination' => $results,
            'search_terms' => $req->term,
            'facets' => $results ? $results->facets() : [],
            'filters' => $results ? $results->filters() : [],
            'empty_message' => trans('documents.empty_msg', ['context' => $group->name]) ]);
    }

    public function edit(AuthGuard $auth, $id)
    {
        $selected_group = Group::findOrFail($id);
        
        if (! is_null($selected_group->project)) {
            return view('panels.prevent_edit', [
                'message' => trans('projects.errors.prevent_edit_description', [
                    'link' => route('projects.edit', ['id' =>$selected_group->project->id]),
                    'name' => $selected_group->project->name
                ]),
                'name' => $selected_group->name
            ]);
        }

        $view_args = [
        'show_cancel' => true,
        'edit' => true,
        'title' => trans('groups.panel_edit_title', ['name' => $selected_group->name]),
        'main_action' => trans('groups.save_btn'),
        'group' => $selected_group];

        return view('panels.group', $view_args);
    }

    /**
     * Update the specified group in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(AuthGuard $auth, $id, UpdateGroupRequest $request)
    {
        // update name and color for personal group
        // if operation o

        try {
            $group = Group::findOrFail($id);
            
            $user = $auth->user();

            if (! $group->is_private && ! $auth->user()->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)) {
                throw new ForbiddenException(trans('errors.group_edit_project'));
            }

            if (! $auth->user()->can_capability(Capability::MANAGE_OWN_GROUPS) && $group->user_id != $auth->user()->id) {
                throw new ForbiddenException(trans('errors.group_edit_else'));
            }

            $current_parent = $group->getParent();

            if ($request->has('name')) {
                $group->name = e($request->input('name'));

                $already_exists = $this->service->checkIfGroupExists($user, $group->name, $current_parent, $group->is_private);

                if ($already_exists) {
                    throw new GroupAlreadyExistsException($group->name, $current_parent);
                }

                $group->save();
            }

            if ($request->has('color')) {
                $group->color = $request->input('color');

                $group->save();
            }

            if (! $request->has('action')) {
                if ($request->has('public')) {
                    if ($group->is_private && ! ! $request->input('public')) {
                        // make public
                        $this->service->makeGroupPublic($auth->user(), $group);
                    }
                } elseif ($request->has('private')) {
                    if (! $group->is_private && ! ! $request->input('private')) {
                        // make private
                        $this->service->makeGroupPrivate($auth->user(), $group);
                    }
                }
            }

            if ($request->has('action') && $request->has('parent')) {
                //action move (default)

                // $parent = $request->input('parent');

                // // get current parent

                $group = $group->fresh();

                $parent_group = Group::findOrFail($request->input('parent'));

                $already_exists = $this->service->checkIfGroupExists($user, $group->name, $parent_group, $group->is_private);

                if ($already_exists) {
                    throw new GroupAlreadyExistsException($group->name, $parent_group);
                }
                
                if ($request->has('public') && $group->is_private && ! ! $request->input('public')) {
                    // make public
                    $group = $this->service->movePersonalCollectionToProject($user, $group, $parent_group);
                } elseif ($request->has('private') && ! $group->is_private && ! ! $request->input('private')) {
                    // make private
                    $group = $this->service->moveProjectCollectionToPersonal($user, $group, $parent_group);
                } elseif ($request->input('action') === 'move' && $parent_group && $group->is_private === $parent_group->is_private) {
                    // move group under different parent
                    $group = $this->service->moveGroup($user, $group, $parent_group);
                }
            }

            \Cache::flush();

            if ($request->wantsJson()) {
                return response()->json($group);
            }

            return response('updated', 200);
        } catch (ForbiddenException $fe) {
            //return forbidden response

            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 403);
            }

            return response("forbidden: ".$fe->getMessage(), 403);
        } catch (GroupAlreadyExistsException $fe) {
            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 409);
            }

            return response($fe->getMessage(), 409);
        } catch (CollectionMoveException $fe) {
            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 409);
            }

            return response($fe->getMessage(), 409);
        } catch (\ExistsException $fe) {
            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 500);
            }

            return response($fe->getMessage(), 500);
        }
    }

    /**
     * Remove the specified group from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(AuthGuard $auth, Request $request, $id)
    {
        // if the group is public only who can manage institution's groups can do this

        try {
            $selected_group = Group::withTrashed()->findOrFail($id);

            $user = $auth->user();
            
            if (! is_null($selected_group->project)) {
                throw new \Exception(trans('projects.errors.prevent_delete_description'));
            }

            $force = $request->input('force', false);
            
            if ($force && ! $user->can_capability(Capability::CLEAN_TRASH)) {
                \Log::warning('User tried to force delete a collection without permission', ['user' => $user->id, 'document' => $id]);
                throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
            }

            if ($force && $selected_group->trashed()) {
                $this->service->permanentlyDeleteGroup($selected_group, $user);
            } else {
                $this->service->deleteGroup($user, $selected_group);
            }
            
            \Cache::flush();

            if ($request->wantsJson()) {
                return new JsonResponse(['status' => 'ok', 'message' => trans('groups.delete.deleted_dialog_title', ['collection' => $selected_group->name])], 202);
            }

            return response('ok', 202);
        } catch (ForbiddenException $fe) {
            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 403);
            }
            
            return response("forbidden", 403);
        } catch (\Exception $fe) {
            if ($request->wantsJson()) {
                return new JsonResponse(['error' => $fe->getMessage()], 500);
            }
            
            return response("generic_error ".$fe->getMessage(), 500);
        }
    }
}
