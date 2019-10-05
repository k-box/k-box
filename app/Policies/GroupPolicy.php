<?php

namespace KBox\Policies;

use KBox\User;
use KBox\Group;
use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\UserConnection;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see
     * the owner of the collection
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Group  $group
     * @return mixed
     */
    public function see_owner(User $user, Group $group)
    {
        if ($user->isDMSAdmin()) {
            return true;
        }

        $owner = $group->user;

        if (is_null($owner)) {
            return false;
        }

        if ($owner->trashed()) {
            return false;
        }

        if ($user->id === $owner->id) {
            return true;
        }

        return UserConnection::exists($user, $owner);
    }
}
