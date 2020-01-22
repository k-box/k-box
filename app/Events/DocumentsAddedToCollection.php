<?php

namespace KBox\Events;

use KBox\Group;

class DocumentsAddedToCollection extends CausedEvent
{
    
    /**
     * @var \KBox\Group
     */
    public $collection;

    /**
     * Identifiers of the added documents
     *
     * @var array
     */
    public $documents;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Group $collection, array $documents)
    {
        parent::__construct();

        $this->collection = $collection;
        $this->documents = $documents;
    }
}
