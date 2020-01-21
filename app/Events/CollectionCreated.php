<?php

namespace KBox\Events;

use KBox\Group;

class CollectionCreated extends CausedEvent
{
    
    /**
     * @var \KBox\Group
     */
    public $collection;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Group $collection)
    {
        parent::__construct();

        $this->collection = $collection;
    }
}
