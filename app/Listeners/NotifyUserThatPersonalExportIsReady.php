<?php

namespace KBox\Listeners;

use KBox\PersonalExport;
use KBox\Events\PersonalExportCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use KBox\Notifications\PersonalExportReadyNotification;

class NotifyUserThatPersonalExportIsReady implements ShouldQueue
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
     * Handle the event.
     *
     * @param  PersonalExportCreated  $event
     * @return void
     */
    public function handle(PersonalExportCreated $event)
    {
        $event->export->user->notify(new PersonalExportReadyNotification($event->export));
    }
}
