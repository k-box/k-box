<?php

namespace KBox\Events;

use KBox\Project;

class ProjectCreated extends CausedEvent
{

    /**
     * @var \KBox\Project
     */
    public $project;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        parent::__construct();

        $this->project = $project;
    }
}
