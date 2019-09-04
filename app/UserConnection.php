<?php

namespace KBox;

class UserConnection
{
    /**
     * Check if a connection between two users exist.
     *
     * A connection can be available because:
     * - A user is in a project that is accessible by both
     * - A user shared previously a document
     *
     * @param \KBox\User $source The user you want to see if is connected
     * @param \KBox\User $targer The user you want to verify the connection against
     * @return boolean
     */
    public static function exists(User $source, User $target)
    {
        // check if current user and owner are at least in a project together
        $projects_of_owner = $target->managedProjects()->get(['projects.id'])->pluck('id')
            ->merge($target->projects()->get(['projects.id'])->pluck('id'))->toArray();

        // maybe here we could use a subselect to get all projects of a user
        
        $project = ProjectMember::where('user_id', $source->id)->whereIn('project_id', $projects_of_owner)->count() > 0 ||
            Project::managedBy($source->id)->whereHas('users', function ($query) use ($target) {
                $query->where('user_id', $target->id);
            })->count() > 0;

        // check if current user and owner have a direct share
        $shared = Shared::sharedWithMe($source)->by($target)->count() > 0 || Shared::sharedWithMe($target)->by($source)->count() > 0;

        return $project || $shared;
    }
}
