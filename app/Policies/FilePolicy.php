<?php

namespace KBox\Policies;

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
}
