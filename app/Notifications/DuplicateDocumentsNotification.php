<?php

namespace KBox\Notifications;

use KBox\DuplicateDocument;
use KBox\Events\FileDuplicateFoundEvent;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Notifications\Dispatcher;

class DuplicateDocumentsNotification extends Notification implements ShouldQueue
{
    public $duplicateEvent;

    public $duplicates;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * Event handler method.
     * This is what is called by Laravel when handling an event
     */
    public function handle(FileDuplicateFoundEvent $event)
    {
        $this->duplicateEvent = $event;

        $duplicates = collect([$event->duplicateDocument]);

        $duplicates = $duplicates->merge(DuplicateDocument::where('id', '<>', $event->duplicateDocument->id)->of($event->user)->notSent()->get());
        
        $this->duplicates = tap($duplicates->where('sent', false), function ($a) {
            $a->each(function ($duplicate) {
                $duplicate->sent = true;
                $duplicate->save();
            });
        });

        if (! $this->duplicates->isEmpty()) {
            app(Dispatcher::class)
                ->sendNow($event->user, $this);
        }
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $language = is_a($notifiable, 'KBox\User') ? $notifiable->optionLanguage(config('app.locale')) : config('app.locale');

        $duplicates = $this->duplicates->map(function ($duplicate) {
            return $duplicate->message;
        });

        $message = (new MailMessage)
            ->markdown('emails.duplicatenotification', ['duplicates' => $duplicates])
            ->subject(trans('mail.duplicatesnotification.subject', [], '', $language))
            ->line(trans('mail.duplicatesnotification.greetings', [], '', $language));
            
        $message->action(trans('mail.duplicatesnotification.action', [], '', $language), route('documents.recent', ['range' => 'today']));

        return $message;
    }
}
