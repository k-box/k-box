<?php

namespace KBox\Events;

use KBox\Shared;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Event that states that a Share of a document to a User has been
 * created
 */
class ShareCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $share = null;

    /**
     * Create a new event instance.
     *
     * @param Shared $share the created Shared instance
     * @return void
     */
    public function __construct(Shared $share)
    {
        $this->share = $share;
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
