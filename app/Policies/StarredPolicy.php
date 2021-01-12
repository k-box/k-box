<?php

namespace KBox\Policies;

use KBox\User;
use KBox\Starred;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Illuminate\Auth\Access\HandlesAuthorization;

class StarredPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any starred entries.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can_capability(Capability::MAKE_SEARCH);
    }

    /**
     * Determine whether the user can view a starred entry.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Starred  $starred
     * @return mixed
     */
    public function view(User $user, Starred $starred)
    {
        return $starred->user->is($user);
    }

    /**
     * Determine whether the user can star a document.
     *
     * A document can be starred if the user can access it
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $document
     * @return mixed
     */
    public function create(User $user, DocumentDescriptor $document)
    {
        return $user->can_capability(Capability::MAKE_SEARCH)
            && $document->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can delete the starred entry.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Starred  $starred
     * @return mixed
     */
    public function delete(User $user, Starred $starred)
    {
        return $starred->user->is($user);
    }

    /**
     * Determine whether the user can permanently delete the starred entry.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Starred  $starred
     * @return mixed
     */
    public function forceDelete(User $user, Starred $starred)
    {
        return $this->delete($user, $starred);
    }
}
