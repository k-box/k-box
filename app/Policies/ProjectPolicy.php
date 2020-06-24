<?php

namespace KBox\Policies;

use KBox\User;
use KBox\Project;
use KBox\Capability;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see projects.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->isDMSManager()) {
            return true;
        }

        if ($user->can_capability(Capability::CREATE_PROJECTS)) {
            return true;
        }

        if ($user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)) {
            return true;
        }

        $managed = $user->projects()->exists();
        $member = $user->managedProjects()->exists();
        
        return $managed || $member;
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return $project->manager->id === $user->id
            || $user->isDMSManager()
            || $project->users()->where('user_id', '=', $user->getKey())->exists();
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can_capability(Capability::CREATE_PROJECTS)
            || $user->isDMSManager();
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $project->manager->id === $user->id
            || $user->isDMSManager();
    }
}
