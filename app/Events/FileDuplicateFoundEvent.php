<?php

namespace KBox\Events;

use KBox\User;
use KBox\DuplicateDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileDuplicateFoundEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \KBox\User;
     */
    public $user;

    /**
     * @var \KBox\DuplicateDocument
     */
    public $duplicateDocument;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, DuplicateDocument $duplicateDocument)
    {
        $this->user = $user;
        $this->duplicateDocument = $duplicateDocument;
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
