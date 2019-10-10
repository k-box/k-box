<?php

namespace KBox\Listeners;

use KBox\Events\ShareCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Log;
use KBox\Notifications\ShareCreatedNotification;
use KBox\User;

/**
 * Handler for the {@see KBox\Events\ShareCreated} event
 *
 * This handler creates and enqueue an email to the user target of the share
 */
class ShareCreatedHandler implements ShouldQueue
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
     * @param  ShareCreated  $event
     * @return void
     */
    public function handle(ShareCreated $event)
    {
        $to = $event->share->sharedwith;

        if (is_a($to, User::class)) {
            Log::info('share created', compact('event'));

            // clean the sidebar cache
            Cache::forget('dms_shared_collections'.$to->id);

            // notify to the user
            $to->notify(new ShareCreatedNotification($event->share));
        }
    }
}
