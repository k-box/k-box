<?php

namespace KBox\Events;

use KBox\Group;

class CollectionTrashed extends CausedEvent
{
    /**
     * @var \KBox\Group
     */
    public $collection;

    /**
     * If the collection was permanently deleted
     * @var bool
     */
    public $forceDeleted = false;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Group $collection)
    {
        parent::__construct();

        $this->collection = $collection;

        $this->forceDeleted = $collection->isForceDeleting();
    }
}
