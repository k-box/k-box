<?php

namespace KBox\Events;

use KBox\DocumentDescriptor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Auth;
use KBox\User;

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
     * The user that triggered the document descriptor restore
     * @var KBox\User|null
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param KBox\DocumentDescriptor The document that has been restored
     * @return void
     */
    public function __construct(DocumentDescriptor $document)
    {
        $this->document = $document;
        $this->user = Auth::user() ?? null;
    }


    /**
     * Specify the user tha caused the event
     * 
     * @param \KBox\User $user 
     * @return self
     */
    public function setCauser(User $user)
    {
        $this->user = $user;
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
