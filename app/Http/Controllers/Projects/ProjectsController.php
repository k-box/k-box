<?php

namespace KBox\Http\Controllers\Projects;

use Exception;
use KBox\User;
use KBox\Project;
use Illuminate\Http\Request;
use KBox\Traits\AvatarUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use KBox\Http\Controllers\Controller;
use KBox\Http\Requests\ProjectRequest;
use KBox\Documents\Services\DocumentsService;

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
    }

    /**
     * Redirect the user to the projects page that
     * shows all projects the user has access.
     *
     * @return Response
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        return redirect()->route('documents.projects.index');
    }

    /**
     * Redirect the user to the project root collection
     * to browse documents within the project.
     *
     * @return Response
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        abort_if(! $project->collection, 404);

        return redirect()->route('documents.groups.show', ['id' => $project->collection->getKey()]);
    }
    
    /**
     * Show the form to edit project details
     *
     * @return Response
     */
    public function edit(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->load('users'); // TODO: maybe already order them by name
        
        /**
         * @var \KBox\User
         */
        $user = $request->user();

        $current_members = $project->users()->orderBy('name', 'ASC')->get(); // TODO: maybe use project users already loaded relationship

        $skip = $current_members->merge(! $user->isDMSManager() ? [$project->manager, $user] : [$project->manager])->filter();

        $available_users = $this->getAvailableUsers($skip);

        return view('projects.edit', [
            'pagetitle' => trans('projects.edit_page_title', ['name' => $project->name]),
            'available_users' => $available_users,
            'project' => $project,
            'project_users' => $current_members,
        ]);
    }
    
    /**
     * Show the form to create a project
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Project::class);

        $available_users = $this->getAvailableUsers($request->user());

        return view('projects.create', [
            'pagetitle' => trans('projects.create_page_title'),
            'available_users' => $available_users,
        ]);
    }
    
    /**
     * Save a new \KBox\Project
     *
     * @return Response
     */
    public function store(ProjectRequest $request, DocumentsService $service)
    {
        $this->authorize('create', Project::class);

        try {
            $manager = $request->user();

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
        } catch (Exception $ex) {
            logs()->error('Error creating project', ['context' => 'ProjectsController', 'params' => $request, 'exception' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])], 500);
            }
            
            return redirect()->back()->withInput()->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
            );
        }
    }

    /**
     * Update an existing \KBox\Project
     *
     * @return Response
     */
    public function update(Project $project, ProjectRequest $request, DocumentsService $service)
    {
        $this->authorize('update', $project);
        
        try {
            $project->load(['collection', 'users']);

            $manager = $request->user();

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
        } catch (Exception $ex) {
            logs()->error('Error updating project', ['context' => 'ProjectsController', 'params' => $request, 'exception' => $ex]);

            if ($request->wantsJson()) {
                return new JsonResponse(['status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])], 500);
            }
            
            return redirect()->back()->withInput()->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
            );
        }
    }

    /**
     * Get the list of users that can be added to the
     * project as members
     *
     * @param \KBox\User|\Illuminate\Support\Collection|array $excludeUsers The users to exclude from the list
     * @return \Illuminate\Support\Collection
     */
    private function getAvailableUsers($excludeUsers = null)
    {
        $skip = [];

        if ($excludeUsers && class_basename(get_class($excludeUsers)) === 'User') {
            $skip[] = $excludeUsers->id;
        } elseif ($excludeUsers && class_basename(get_class($excludeUsers)) === 'Collection') {
            $skip = $excludeUsers->pluck('id')->all();
        } elseif ($excludeUsers && is_array($excludeUsers)) {
            $skip = array_merge($skip, $excludeUsers);
        }

        return User::whereNotIn('id', $skip)->orderBy('name', 'ASC')->get();
    }
}
