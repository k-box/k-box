<?php

namespace KBox\Events;

use KBox\DocumentDescriptor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Document Descriptor deleted event
 *
 * It is fired when a descriptor is trashed or permanently deleted.
 */
class DocumentDescriptorDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
        $this->document = $document;
        $this->forceDeleted = $document->isForceDeleting();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
