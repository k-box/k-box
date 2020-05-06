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
        $can_create = $user->can_capability(Capability::CREATE_PROJECTS);

        if ($can_create) {
            return true;
        }

        $can = $user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS);

        if (config('dms.hide_projects_if_empty')) {
            $managed = $user->projects()->exists();
            $member = $user->managedProjects()->exists();

            return $can && ($managed || $member);
        }

        return $can;
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
        //
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
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
        //
    }
}
