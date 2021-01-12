<?php

namespace KBox\Policies;

use KBox\User;
use KBox\Group;
use Illuminate\Auth\Access\HandlesAuthorization;
use KBox\Capability;
use KBox\UserConnection;
use KBox\Documents\Services\DocumentsService;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * [$adapter description]
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DocumentsService $service)
    {
        $this->service = $service;
    }

    /**
     * Determine whether the user can see
     * the owner of the collection
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Group  $group
     * @return mixed
     */
    public function see_owner(User $user, Group $group)
    {
        if ($user->isDMSAdmin()) {
            return true;
        }

        $owner = $group->user;

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
        return $user->can_capability([Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Group $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->can_capability([Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS])
            && $this->service->isCollectionAccessible($user, $group);
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
     * @param  \KBox\Group $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        if (! $group->is_private && ! $user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)) {
            Response::deny(trans('errors.group_edit_project'));
        }

        if (! $user->can_capability(Capability::MANAGE_OWN_GROUPS) && $group->user_id != $user->id) {
            Response::deny(trans('errors.group_edit_else'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Group $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $user->can_capability([Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS])
            && $this->service->isCollectionAccessible($user, $group);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Group $group
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
        return $user->can_capability([Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \KBox\User  $user
     * @param  \KBox\Group $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
        return $user->can_capability([Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS]) && $user->can_capability(Capability::CLEAN_TRASH);
    }
}
