<?php

namespace KBox\Notifications;

use KBox\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification sent to the newly created user
 */
class UserCreatedNotification extends Notification
{
    use Queueable;

    /**
     * The user that has been created
     * @var \KBox\User
     */
    private $user = null;

    /**
     * The password in plain text, as needs to be shown to the user
     *
     * @var string
     */
    private $password = null;

    /**
     * Create a new notification instance.
     *
     * @param KBox\User $user the created user
     * @param string $password the password assigned to the user, in plain text
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
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
        return (new MailMessage)
                    ->subject(trans('administration.accounts.mail_subject'))
                    ->greeting(trans('mail.welcome.welcome', ['name' => $this->user->name]))
                    ->line(trans('mail.welcome.credentials_alt'))
                    ->line(trans('mail.welcome.username', ['mail' => $this->user->email]))
                    ->line(trans('mail.welcome.password', ['password' => $this->password]))
                    ->salutation(trans('messaging.mail.do_not_reply'))
                    ->action(trans('mail.welcome.login_button_alt'), config('app.url'));
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
