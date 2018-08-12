<?php

namespace KBox\Events;

use KBox\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * File deleted event
 *
 * It is fired when a file is trashed or permanently deleted.
 */
class FileDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The file that has been deleted
     * @var KBox\File
     */
    public $file;

    /**
     * If the file was permanently deleted
     * @var bool
     */
    public $forceDeleted = false;

    /**
     * Create a new event instance.
     *
     * @param KBox\File The file that has been deleted
     * @return void
     */
    public function __construct(File $file)
    {
        $this->file = $file;
        $this->forceDeleted = $file->isForceDeleting();
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
