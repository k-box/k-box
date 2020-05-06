<?php

namespace KBox\Http\Controllers\Projects;

use KBox\Http\Requests\ProjectRequest;
use KBox\Http\Controllers\Controller;
use KBox\User;
use KBox\Project;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KBox\Documents\Services\DocumentsService;
use KBox\Traits\AvatarUpload;
use KBox\Exceptions\ForbiddenException;
use Illuminate\Support\Facades\DB;

/**
 * Controller for the Project Management
 */
class ProjectsController extends Controller
{
    use AvatarUpload;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Guard $auth, \Request $request)
    {
        $user = $auth->user();
        
        $projects = Project::managedBy($user->id)->get();
        
        if ($request::wantsJson()) {
            return response()->json($projects);
        }

        return view('projects.index', [
            'pagetitle' => trans('projects.page_title'),
            'projects' => $projects
        ]);
    }

    public function show(Guard $auth, \Request $request, $id)
    {
        try {
            $user = $auth->user();
            
            $project = Project::findOrFail($id)->load(['users', 'manager', 'microsite']);

            $projects = Project::managedBy($user->id)->get();
            
            if ($request::wantsJson()) {
                return response()->json($project);
            }
    
            return view('projects.show', [
                'pagetitle' => trans('projects.page_title_with_name', ['name' => $project->name]),
                'projects' => $projects,
                'project' => $project,
                'project_users' => $project->users()->orderBy('name', 'ASC')->get(),
            ]);
        } catch (\Exception $ex) {
            \Log::error('Error showing project', ['context' => 'ProjectsController', 'params' => $id, 'exception' => $ex]);

            if ($request::wantsJson()) {
                return new JsonResponse(['status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])], 500);
            }
            
            return redirect()->back()->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
            );
        }
    }
    
    public function edit(Guard $auth, $id)
    {
        $prj = Project::findOrFail($id)->load('users');
        
        $user = $auth->user();

        if ($prj->manager->id !== $user->id && ! $user->isDMSManager()) {
            throw new ForbiddenException();
        }

        $current_members = $prj->users()->orderBy('name', 'ASC')->get();

        $skip = $current_members->merge(! $user->isDMSManager() ? [$prj->manager, $user] : [$prj->manager])->filter();

        $available_users = $this->getAvailableUsers($skip);

        return view('projects.edit', [
            'pagetitle' => trans('projects.edit_page_title', ['name' => $prj->name]),
            'available_users' => $available_users,
            'manager_id' => optional($prj->manager)->id,
            'project' => $prj,
            'project_users' => $current_members,
        ]);
    }
    
    public function create(Guard $auth)
    {
        $user = $auth->user();

        $available_users = $this->getAvailableUsers($user);

        return view('projects.create', [
            'pagetitle' => trans('projects.create_page_title'),
            'available_users' => $available_users,
            'manager_id' => $user->id,
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Guard $auth, ProjectRequest $request, DocumentsService $service)
    {
        try {
            $user = $auth->user();

            $manager = $request->has('manager') ? User::findOrFail($request->get('manager')) : $user;

            $avatar = $this->avatarStore($request, $manager->id);

            $project = DB::transaction(function () use ($manager, $request, $service, $avatar) {
                $name = $request->input('name');
                
                $projectcollection = $service->createGroup($manager, $name, null, null, false);
                
                $newProject = Project::create([
                    'user_id' => $manager->id,
                    'name' => e(trim($name)),
                    'description' => e($request->input('description', '')),
                    'collection_id' => $projectcollection->id,
                    'avatar' => $avatar
                    ]);
                    
                return $newProject;
            });
            
            if ($request->has('users')) {
                DB::transaction(function () use ($project, $request) {
                    $users = $request->get('users');
                    
                    $project->addMembers($users);
                });
            }

            \Cache::flush();

            if ($request->wantsJson()) {
                return response()->json($project);
            }
            
            return redirect()->route('documents.groups.show', $project->collection_id)->with([
                'flash_message' => trans('projects.project_created', ['name' => $project->name])
            ]);
        } catch (\Exception $ex) {
            \Log::error('Error creating project', ['context' => 'ProjectsController', 'params' => $request, 'exception' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])], 500);
            }
            
            return redirect()->back()->withInput()->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
            );
        }
    }

    public function update(Guard $auth, ProjectRequest $request, DocumentsService $service, $id)
    {
        try {
            $project = Project::findOrFail($id)->load(['collection', 'users']);

            $user = $auth->user();
            
            $manager = $request->has('manager') ? User::findOrFail($request->get('manager')) : $user;

            $avatar = $this->avatarStore($request, $project->id);

            $project = DB::transaction(function () use ($manager, $request, $service, $project, $avatar) {
                if ($request->has('name') && $project->name !== $request->input('name')) {
                    //rename project and collection
                    
                    $project->name = e(trim($request->input('name')));
                    
                    $projectcollection = $service->updateGroup($manager, $project->collection, ['name' => $project->name]);
                    
                    $project->save();
                }
                
                if ($request->has('description') && $project->description !== $request->input('description')) {
                    $project->description = e($request->input('description'));
                    $project->save();
                } elseif (! $request->has('description') && ! empty($project->description)) {
                    $project->description = '';
                    $project->save();
                }

                if (! is_null($avatar)) {
                    $project->avatar = $avatar;
                    $project->save();
                }
                
                // test if there are users to add/remove to/from the project
                if ($request->has('users')) {
                    $users = $request->get('users');
                    // users are ID
                
                    $prj_users = $project->users->pluck('id')->all();
                    $users_to_add = array_diff($users, $prj_users);
                    $users_to_remove = array_intersect($prj_users, $users);
                    
                    if (count($users_to_add) > 0) {
                        $project->addMembers($users_to_add);
                    }
                    
                    if (count($users_to_remove) > 0) {
                        $project->removeMembers($users_to_remove);
                    }
                }
                
                return $project->fresh();
            });

            \Cache::flush();

            if ($request->wantsJson()) {
                return response()->json($project);
            }
            
            return redirect()->route('projects.edit', ['id' => $project->id])->with([
                'flash_message' => trans('projects.project_updated', ['name' => $project->name])
            ]);
        } catch (\Exception $ex) {
            \Log::error('Error updating project', ['context' => 'ProjectsController', 'params' => $request, 'exception' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])], 500);
            }
            
            return redirect()->back()->withInput()/*->route('projects.create')*/->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
            );
        }
    }

    /**
     * Filter the list of users that can be added to a project
     */
    private function getAvailableUsers($users)
    {
        $skip = [];

        if (class_basename(get_class($users)) === 'User') {
            $skip[] = $users->id;
        } elseif (class_basename(get_class($users)) === 'Collection') {
            $skip = $users->pluck('id')->all();
        } elseif (is_array($users)) {
            $skip = array_merge($skip, $users);
        }

        return User::whereNotIn('id', $skip)->orderBy('name', 'ASC')->get();
    }
}
