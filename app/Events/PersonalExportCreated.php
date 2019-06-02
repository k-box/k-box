<?php

namespace KBox\Events;

use KBox\PersonalExport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PersonalExportCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \KBox\PersonalExport;
     */
    public $export = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PersonalExport $export)
    {
        $this->export = $export;
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
