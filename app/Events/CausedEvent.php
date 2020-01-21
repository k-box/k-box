<?php

namespace KBox\Events;

use KBox\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Auth;

/**
 * Events that are caused by a user
 */
abstract class CausedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that triggered the document descriptor deletion
     * @var KBox\User|null
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param KBox\DocumentDescriptor The document that has been deleted
     * @return void
     */
    public function __construct()
    {
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

        return $this;
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
