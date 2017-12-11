<?php

namespace KBox\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A notification about a User sharing a document or collection
 * with another user
 */
class ShareCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Who shared the document or the collection
     *
     * @var KBox\User
     */
    private $from;

    /**
     * What has been shared
     *
     * @var KBox\Group|KBox\DocumentDescriptor
     */
    private $what;

    /**
     * The share that has been created
     *
     * @var KBox\Shared
     */
    private $share;

    /**
     * Create a new notification instance.
     *
     * @param KBox\Shared $share The share created
     * @return void
     */
    public function __construct($share)
    {
        $this->what = $share->shareable;
        $this->from = $share->user;
        $this->share = $share;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $language = is_a($notifiable, 'KBox\User') ? $notifiable->optionLanguage(config('app.locale')) : config('app.locale');

        $is_collection = is_a($this->what, 'KBox\Group') ? true : false;
        $item_title = $is_collection ? $this->what->name : $this->what->title;
        $share_link = rtrim(config('app.url'), '/').route('shares.show', ['id' => $this->share->token], false);

        return (new MailMessage)
                    ->subject(trans('mail.sharecreated.subject', ['user' => $this->from->name, 'title' => $item_title], '', $language))
                    ->line(trans($is_collection ? 'mail.sharecreated.shared_collection_with_you' : 'mail.sharecreated.shared_document_with_you', ['user' => $this->from->name], '', $language))
                    ->action($item_title, $share_link);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
