<?php

namespace KBox\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\Capability;
use KBox\Publication;
use KBox\User;

class PublicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create a publication.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can_capability(Capability::PUBLISH_TO_KLINK);
    }

    /**
     * Determine whether the user can update an existing publication.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Publication  $publication
     * @return mixed
     */
    public function update(User $user, Publication $publication)
    {
        return $user->can_capability(Capability::PUBLISH_TO_KLINK);
    }

    /**
     * Determine whether the user can unpublish.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Publication  $publication
     * @return mixed
     */
    public function delete(User $user, Publication $publication)
    {
        return $user->can_capability(Capability::PUBLISH_TO_KLINK);
    }
}
