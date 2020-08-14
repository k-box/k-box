<?php

namespace KBox\Policies;

use KBox\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * User account policies.
 *
 * Define when users are authorized to manage
 * other users inside the K-Box
 */
class UserPolicy
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
        return $user->isDMSManager();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->isDMSManager();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isDMSManager();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->isDMSManager();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->isDMSManager();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return $user->isDMSManager();
    }
}
