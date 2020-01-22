<?php

namespace KBox\Events;

use KBox\Project;

class ProjectMembersAdded extends ProjectCreated
{
    public $members = [];

    /**
     * Create a new event instance.
     *
     * @param \KBox\Project
     * @param \KBox\User[]
     * @return void
     */
    public function __construct(Project $project, array $users)
    {
        parent::__construct($project);

        $this->members = $users;
    }
}
