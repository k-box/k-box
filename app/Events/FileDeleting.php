<?php

namespace KBox\Events;

use KBox\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Auth;
use KBox\User;

/**
 * File deleting event
 *
 * It is fired immediately before a file is trashed or permanently deleted.
 */
class FileDeleting
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The file that has been deleted
     * @var KBox\File
     */
    public $file;

    /**
     * The user that triggered the document descriptor restore
     * 
     * It might
     * 
     * @var KBox\User|null
     */
    public $user;

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
