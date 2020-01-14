<?php

namespace KBox\Events;

use KBox\DocumentDescriptor;

/**
 * Document Descriptor restored event
 *
 * It is fired when a descriptor is restored from trash.
 */
class DocumentDescriptorRestored extends CausedEvent
{

    /**
     * The DocumentDescriptor that has been restored
     * @var KBox\DocumentDescriptor
     */
    public $document;

    /**
     * Create a new event instance.
     *
     * @param KBox\DocumentDescriptor The document that has been restored
     * @return void
     */
    public function __construct(DocumentDescriptor $document)
    {
        parent::__construct();

        $this->document = $document;
    }
}
