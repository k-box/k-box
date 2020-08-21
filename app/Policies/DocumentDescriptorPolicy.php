<?php

namespace KBox\Policies;

use KBox\Capability;
use KBox\User;
use KBox\UserConnection;
use KBox\DocumentDescriptor;

class DocumentDescriptorPolicy
{

    /**
     * Determine whether the user can see the document descriptor.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $documentDescriptor
     * @return mixed
     */
    public function see(User $user, DocumentDescriptor $documentDescriptor)
    {

        // the user is the admin
        if ($user->isDMSAdmin()) {
            return true;
        }

        // the user is the owner
        if ($user->id === $documentDescriptor->owner_id) {
            return true;
        }

        return $documentDescriptor->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can see the owner user of the document descriptor.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $documentDescriptor
     * @return mixed
     */
    public function see_owner(User $user, DocumentDescriptor $documentDescriptor)
    {
        if ($user->isDMSManager()) {
            return true;
        }

        $owner = $documentDescriptor->owner;

        if (is_null($owner)) {
            return false;
        }

        if ($owner->trashed()) {
            return false;
        }

        if ($user->id === $owner->id) {
            return true;
        }

        return UserConnection::exists($user, $owner);
    }

    /**
     * Determine whether the user can update the document.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $document
     * @return mixed
     */
    public function update(User $user, DocumentDescriptor $document)
    {
        return $user->can_capability(Capability::EDIT_DOCUMENT) &&
               $document->isEditableBy($user);
    }
}
