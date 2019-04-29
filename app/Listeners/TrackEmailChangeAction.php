<?php

namespace KBox\Listeners;

use KBox\Events\EmailChanged;

class TrackEmailChangeAction
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
     * Handle the event.
     *
     * @param  EmailChanged  $event
     * @return void
     */
    public function handle(EmailChanged $event)
    {
        activity('security')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties([
                'from' => $event->from,
                'to' => $event->to,
            ])
            ->log('email_changed');
    }
}
