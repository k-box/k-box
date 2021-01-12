<?php

namespace KBox\Policies;

use KBox\Capability;
use KBox\User;
use KBox\File;
use KBox\UserConnection;

class FilePolicy
{
    /**
     * Determine whether the user can see the uploader of the file.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\File  $file
     * @return mixed
     */
    public function see_uploader(User $user, File $file)
    {
        if ($user->isDMSAdmin()) {
            return true;
        }

        $uploader = $file->user;

        if (is_null($uploader)) {
            return false;
        }

        if ($uploader->trashed()) {
            return false;
        }

        if ($user->id === $uploader->id) {
            return true;
        }

        return UserConnection::exists($user, $uploader);
    }

    /**
     * Determine whether the user can view a file version.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\File  $file
     * @return mixed
     */
    public function view(User $user, File $file)
    {
        return $user->can_capability(Capability::EDIT_DOCUMENT);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can_capability(Capability::EDIT_DOCUMENT);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\File  $file
     * @return mixed
     */
    public function delete(User $user, File $file)
    {
        return $user->can_capability(Capability::EDIT_DOCUMENT);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\File  $file
     * @return mixed
     */
    public function restore(User $user, File $file)
    {
        return $user->can_capability(Capability::EDIT_DOCUMENT);
    }
}
