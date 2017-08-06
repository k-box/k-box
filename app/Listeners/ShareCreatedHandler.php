<?php

namespace KlinkDMS\Listeners;

use KlinkDMS\Events\ShareCreated;
use KlinkDMS\Option;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
// use Mail;
use KlinkDMS\Notifications\ShareCreatedNotification;

/**
 * Handler for the {@see KlinkDMS\Events\ShareCreated} event
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
        // $from = $event->share->user;
        $to = $event->share->sharedwith;
        // $what = $event->share->shareable;

        if (is_a($to, 'KlinkDMS\User')) {
            Log::info('share created', compact('event'));
            // Log::info('share to user, creating ShareCreatedNotification', compact('from', 'to', 'what'));

            $to->notify(new ShareCreatedNotification($event->share));

            // Mail::send('emails.shares.created', $data, function ($message) use($from, $to, $what_title, $language, $subject) {
            //     $message->subject($subject);

            //     $message->from(Option::mailFrom(), $from->name);
            //     $message->replyTo($from->email, $from->name);

            //     if(is_a($to, 'KlinkDMS\PeopleGroup'))
            //     {
            //         $recipients = $to->people;

            //         foreach ($recipients as $recipient) {
            //             $message->to($recipient->email, $recipient->name);
            //         }
            //     }
            //     else {
            //         $message->to($to->email, $to->name);
            //     }
            // });
        }
    }
}
