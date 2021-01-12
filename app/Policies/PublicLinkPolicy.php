<?php

namespace KBox\Policies;

use KBox\User;
use KBox\PublicLink;
use KBox\Capability;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicLinkPolicy
{
    use HandlesAuthorization;

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
     * Determine whether the user can update the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\PublicLink  $publicLink
     * @return mixed
     */
    public function update(User $user, PublicLink $publicLink)
    {
        return $user->can_capability(Capability::SHARE_WITH_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\PublicLink  $publicLink
     * @return mixed
     */
    public function delete(User $user, PublicLink $publicLink)
    {
        return $user->can_capability(Capability::SHARE_WITH_USERS);
    }
}
