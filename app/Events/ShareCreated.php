<?php

namespace KlinkDMS\Events;

use KlinkDMS\Shared;
use KlinkDMS\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Event that states that a Share of a document to a User has been
 * created
 */
class ShareCreated extends Event
{
    use SerializesModels;

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
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
