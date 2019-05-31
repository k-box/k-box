<?php

namespace KBox\Listeners;

use KBox\Events\PersonalExportReady;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  PersonalExportReady  $event
     * @return void
     */
    public function handle(PersonalExportReady $event)
    {
        //
    }
}
