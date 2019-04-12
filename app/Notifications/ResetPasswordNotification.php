<?php

namespace KBox\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;
    
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $language = $notifiable && is_a($notifiable, \KBox\User::class) ? $notifiable->optionLanguage(config('app.locale')) : config('app.locale');

        return (new MailMessage)
            ->line(trans('mail.password_reset.you_are_receiving_because', [], '', $language)) // the [] is the argument that carries the placeholders substitution
            ->action(trans('mail.password_reset.reset_password', [], '', $language), url('password/reset', $this->token))
            ->salutation(trans('messaging.mail.do_not_reply', [], '', $language))
            ->line(trans('mail.password_reset.no_further_action', [], '', $language));
    }
}
