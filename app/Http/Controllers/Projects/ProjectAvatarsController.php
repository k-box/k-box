<?php

namespace KBox\Http\Controllers\Projects;

use KBox\Http\Requests\AvatarRequest;
use KBox\Http\Controllers\Controller;
use KBox\Project;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KBox\Traits\AvatarUpload;

/**
 * Controller for the management of the Project Avatar
 */
class ProjectAvatarsController extends Controller
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
     * Display the project avatar.
     *
     * @return Response
     */
    public function index($id)
    {
        $project = Project::findOrFail($id);

        $this->authorize('view', $project);

        return response()->download($project->avatar);
    }

    /**
     * Store a new avatar for the project.
     *
     * @return Response
     */
    public function store(AvatarRequest $request, $id)
    {
        $project = Project::findOrFail($id);

        $this->authorize('update', $project);

        if ($request->user()->id !== $project->user_id) {
            return new JsonResponse(['status' => 'error', 'error' => 'Forbidden'], 403);
        }

        $avatar = $this->avatarStore($request, $project->manager->id);

        $project->avatar = $avatar;
        
        $project->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the project avatar.
     *
     * @param  int  $id the ID of the project to remove the avatar from
     * @return Response
     */
    public function destroy(Guard $auth, $id)
    {
        $project = Project::findOrFail($id);

        $this->authorize('update', $project);

        if ($auth->user()->id !== $project->user_id) {
            return new JsonResponse(['status' => 'error', 'error' => 'Forbidden'], 403);
        }

        $avatar_file = $project->avatar;

        unlink($avatar_file);
        
        $project->avatar = null;
        
        $project->save();

        return response()->json(['status' => 'ok']);
    }
}
