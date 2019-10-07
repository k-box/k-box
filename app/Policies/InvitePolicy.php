<?php

namespace KBox\Policies;

use KBox\User;
use KBox\Invite;
use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\Auth\Registration;

class InvitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create invites.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return Registration::isEnabled() && $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can delete the invite.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Invite  $invite
     * @return mixed
     */
    public function delete(User $user, Invite $invite)
    {
        return $invite->wasCreatedBy($user);
    }
}
