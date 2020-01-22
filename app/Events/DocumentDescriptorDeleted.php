<?php

namespace KBox\Events;

use KBox\DocumentDescriptor;

/**
 * Document Descriptor deleted event
 *
 * It is fired when a descriptor is trashed or permanently deleted.
 */
class DocumentDescriptorDeleted extends CausedEvent
{

    /**
     * The DocumentDescriptor that has been deleted
     * @var KBox\DocumentDescriptor
     */
    public $document;
    
    /**
     * If the document was permanently deleted
     * @var bool
     */
    public $forceDeleted = false;

    /**
     * Create a new event instance.
     *
     * @param KBox\DocumentDescriptor The document that has been deleted
     * @return void
     */
    public function __construct(DocumentDescriptor $document)
    {
        parent::__construct();

        $this->document = $document;
        $this->forceDeleted = $document->isForceDeleting();
    }
}
