<?php namespace KlinkDMS\Traits;

use KlinkDMS\Http\Requests\Request;
use Carbon\Carbon;

/**
 * Add utility methods to handle avatars in a consistent 
 * way accross all the DMS
 */
trait AvatarUpload
{
    /**
     * Store the avatar contained in the request avatar field and 
     * returns the storage path
     *
     * @return string|null the absolute path to the stored avatar file. Null if no avatar field is available
     */
    protected function avatarStore(Request $request, $prefix = '')
    {
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()){
            // check file size
            // check image dimension
            $avatar_name = md5($prefix . Carbon::now()->timestamp) . '.' . $request->file('avatar')->guessExtension();
            $avatar = storage_path('app/projects/avatars/' . $avatar_name);
            $request->file('avatar')->move(storage_path('app/projects/avatars'), $avatar_name );
            return $avatar;
        }

        return null; 
    }
}