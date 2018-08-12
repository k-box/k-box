<?php

namespace KBox\Events;

use KBox\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * File restored event
 *
 * It is fired when a file is restored from trash.
 */
class FileRestored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The file that has been restored
     * @var KBox\File
     */
    public $file;

    /**
     * Create a new event instance.
     *
     * @param KBox\File The file that has been restored
     * @return void
     */
    public function __construct(File $file)
    {
        $this->file = $file;
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
