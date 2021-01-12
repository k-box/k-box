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
     * Determine whether the user can view any models.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can_capability(Capability::MAKE_SEARCH);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $document
     * @return mixed
     */
    public function view(User $user, DocumentDescriptor $document)
    {
        return $user->can_capability(Capability::MAKE_SEARCH)
            && $document->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \KBox\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can_capability(Capability::UPLOAD_DOCUMENTS);
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

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $document
     * @return mixed
     */
    public function delete(User $user, DocumentDescriptor $document = null)
    {
        return $user->can_capability(Capability::DELETE_DOCUMENT);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $document
     * @return mixed
     */
    public function restore(User $user, DocumentDescriptor $document = null)
    {
        return $user->can_capability(Capability::DELETE_DOCUMENT);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\DocumentDescriptor  $document
     * @return mixed
     */
    public function forceDelete(User $user, DocumentDescriptor $document = null)
    {
        // TODO: check if the user is the uploader of the document
        return $user->can_capability(Capability::CLEAN_TRASH);
    }
}
