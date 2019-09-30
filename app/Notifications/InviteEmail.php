<?php

namespace KBox\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use KBox\Invite;
use Illuminate\Support\Str;

/**
 * The email that will be sent to invite a user to
 * join the K-Box by registering an account
 *
 * This is a default template
 *
 * InviteEmail::toMailUsing(function ($notifiable, $registrationUrl, $reason) {
 *           return (new MailMessage)
 *               ->subject('Welcome!');
 *       });
 *
 */
class InviteEmail extends Notification
{

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
    public function toMail(Invite $notifiable)
    {
        $registrationUrl = $this->registrationUrl($notifiable);
        
        $reason = $this->getReason($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $registrationUrl, $reason);
        }

        return (new MailMessage)
            ->subject(trans('invite.notification.mail.subject', ['name' => $notifiable->creator->name]))
            ->greeting(trans('invite.notification.mail.greeting', ['name' => $notifiable->creator->name, 'url' => url('/')]))
            ->line(trans('invite.notification.mail.reason.'.$reason, ['name' => $notifiable->creator->name, 'url' => url('/')]))
            ->action(trans('auth.create_account'), $registrationUrl)
            ->salutation(trans('messaging.mail.do_not_reply'))
            ->line(trans('invite.notification.mail.no_further_action', ['date' => $notifiable->expire_at->toDateString()]));
    }

    /**
     * Get the registration URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function registrationUrl(Invite $notifiable)
    {
        return URL::signedRoute(
            'register',
            [
                'i' => $notifiable->uuid,
                'e' => $notifiable->email,
            ]
        );
    }
    
    /**
     * Get reason for this invitation.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function getReason(Invite $notifiable)
    {
        if (is_null($notifiable->actionable)) {
            return 'invitation';
        }

        return Str::slug(class_basename($notifiable->actionable));
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
