<?php

namespace KBox\Events;

use KBox\DocumentDescriptor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Document Descriptor restored event
 *
 * It is fired when a descriptor is restored from trash.
 */
class DocumentDescriptorRestored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
        $this->document = $document;
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
