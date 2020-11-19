<?php

namespace KBox\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\Identity;
use KBox\User;

class IdentityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Identity  $identity
     * @return mixed
     */
    public function update(User $user, Identity $identity)
    {
        return $user->getKey() === $identity->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Identity  $identity
     * @return mixed
     */
    public function delete(User $user, Identity $identity)
    {
        return $user->getKey() === $identity->user_id;
    }
}
