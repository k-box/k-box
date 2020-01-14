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
     * The user that triggered the document descriptor restore
     * 
     * It might
     * 
     * @var KBox\User|null
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param KBox\File The file that has been restored
     * @return void
     */
    public function __construct(File $file)
    {
        $this->file = $file;
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
