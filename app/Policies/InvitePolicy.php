<?php

namespace KBox\Policies;

use KBox\User;
use KBox\Invite;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the invite.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Invite  $invite
     * @return mixed
     */
    public function view(User $user, Invite $invite)
    {
        //
    }

    /**
     * Determine whether the user can create invites.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return filter_var(config('dms.registration'), FILTER_VALIDATE_BOOLEAN) && $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the invite.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Invite  $invite
     * @return mixed
     */
    public function update(User $user, Invite $invite)
    {
        return $invite->wasCreatedBy($user);
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
