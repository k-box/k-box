<?php

namespace KlinkDMS\Listeners;

use KlinkDMS\Events\ShareCreated;
use KlinkDMS\Option;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Mail;

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
        $from = $event->share->user;
        $to = $event->share->sharedwith;
        $what = $event->share->shareable;
        $is_collection = is_a($what, 'KlinkDMS\Group') ? true : false;
        $what_title = $is_collection ? $what->name : $what->title;
        $language = is_a($to, 'KlinkDMS\PeopleGroup') ? config('app.locale') : $to->optionLanguage(config('app.locale'));
        $subject = trans('mail.sharecreated.subject', ['user' => $from->name, 'title' => $what_title], '', $language);

        $data = [
            'name' => $from->name,
            'title' => $what_title,
            'share_link' => config('app.url') . route('shares.show', ['id' => $event->share->token], false),
            'is_collection' => $is_collection,
            'language' =>  $language,
            'subject' => trans('mail.sharecreated.subject', ['user' => $from->name, 'title' => $what_title], '', $language),
        ];

        Mail::send('emails.shares.created', $data, function ($message) use($from, $to, $what_title, $language, $subject) {

            $message->subject($subject);

            $message->from(Option::mailFrom(), $from->name);
            $message->replyTo($from->email, $from->name);

            if(is_a($to, 'KlinkDMS\PeopleGroup'))
            {
                $recipients = $to->people;

                foreach ($recipients as $recipient) {
                    $message->to($recipient->email, $recipient->name);
                }
            }
            else {
                $message->to($to->email, $to->name);
            }


        });
    }
}
