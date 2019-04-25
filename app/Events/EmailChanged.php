<?php

namespace KBox\Events;

use Illuminate\Queue\SerializesModels;

class EmailChanged
{
    use SerializesModels;

    /**
     * The user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;
    
    /**
     * The previous email address
     *
     * @var string
     */
    public $from;
    
    /**
     * The new email address
     *
     * @var string
     */
    public $to;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($user, $old, $new)
    {
        $this->user = $user;
        $this->from = $old;
        $this->to = $new;
    }
}
