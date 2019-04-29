<?php

namespace KBox\Listeners;

use Illuminate\Auth\Events\Verified;

class TrackEmailVerification
{
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
     * Save that the user verified the email address
     *
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        activity('security')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties([
                'email' => $event->user->email,
            ])
            ->log('email_verified');
    }
}
