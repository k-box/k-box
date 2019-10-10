<?php

namespace KBox\Policies;

use KBox\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\Capability;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see projects.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function viewAll(User $user)
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
}
