<?php

namespace KBox\Policies;

use Log;
use KBox\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\UploadedFile;
use KBox\Capability;
use KBox\Facades\UserQuota;
use OneOffTech\TusUpload\Http\Requests\CreateUploadRequest;

class UploadPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can upload a file.
     *
     * The user should have UPLOAD_DOCUMENTS capability and the
     * file being uploaded should fit into the available storage
     * quota assigned to the user
     *
     * @param  \KBox\User  $user
     * @param  \Illuminate\Http\UploadedFile  $upload
     * @return mixed
     */
    public function uploadFile(User $user, UploadedFile $upload)
    {
        return $user->can_capability(Capability::UPLOAD_DOCUMENTS)
               && UserQuota::accept($upload->getSize(), $user);
    }
    
    /**
     * Determine if the user can perform an upload using the resumable upload
     *
     * @param  \KBox\User  $user
     * @param  \OneOffTech\TusUpload\Http\Requests\CreateUploadRequest  $uploadRequest
     * @return mixed
     */
    public function uploadFileViaTus(User $user, CreateUploadRequest $uploadRequest)
    {
        Log::info('Gate: Tus upload request', ['user' => $user->id, 'upload_request' => $uploadRequest->all()]);

        return $user->can_capability(Capability::UPLOAD_DOCUMENTS)
               && UserQuota::accept($uploadRequest->input('filesize'), $user);
    }
}
