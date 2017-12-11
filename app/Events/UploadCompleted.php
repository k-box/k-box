<?php

namespace KBox\Events;

use KBox\User;
use KBox\DocumentDescriptor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UploadCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \KBox\DocumentDescriptor
     */
     public $descriptor = null;
     
     /**
      * @var \KBox\User
      */
    public $user = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DocumentDescriptor $descriptor, User $user)
    {
        $this->descriptor = $descriptor;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
