<?php

namespace KBox\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\Capability;
use KBox\Shared;
use KBox\User;

class SharedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can_capability(Capability::RECEIVE_AND_SEE_SHARE);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Shared  $shared
     * @return mixed
     */
    public function view(User $user, Shared $shared)
    {
        return $user->can_capability(Capability::RECEIVE_AND_SEE_SHARE);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can_capability(Capability::SHARE_WITH_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Shared  $shared
     * @return mixed
     */
    public function delete(User $user, Shared $shared)
    {
        return $user->can_capability(Capability::SHARE_WITH_USERS) &&
            ($shared->user->is($user) || $shared->sharedwith->is($user));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Shared  $shared
     * @return mixed
     */
    public function forceDelete(User $user, Shared $shared)
    {
        return $this->delete($user, $shared);
    }
}
