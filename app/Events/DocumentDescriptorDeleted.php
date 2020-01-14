<?php

namespace KBox\Events;

use KBox\User;
use KBox\DocumentDescriptor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Auth;

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
     * The user that triggered the document descriptor deletion
     * @var KBox\User|null
     */
    public $user;
    
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
