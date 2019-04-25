<?php

namespace KBox\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackPasswordResetAction implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Log the user's password reset activity
     *
     * @param  PasswordReset  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        activity('security')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->log('password_reset');
    }
}
