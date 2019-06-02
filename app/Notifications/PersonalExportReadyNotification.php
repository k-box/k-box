<?php

namespace KBox\Notifications;

use KBox\PersonalExport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PersonalExportReadyNotification extends Notification
{
    
    use Queueable;

    /**
     * @var \KBox\PersonalExport;
     */
    public $export = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(PersonalExport $export)
    {
        $this->export = $export;
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
        $language = is_a($notifiable, \KBox\User::class) ? $notifiable->optionLanguage(config('app.locale')) : config('app.locale');
        
        return (new MailMessage)
                    ->line(trans('mail.data-export.description', [], '', $language))
                    ->action(trans('mail.data-export.action', [], '', $language), route('profile.data-export.download', ['name' => $this->export->name]))
                    ->line(trans('mail.data-export.expiration', ['expire' => $this->export->getPurgeAt()], '', $language));
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
