<?php

namespace KBox\Mail;

use KBox\User;
use KBox\Option;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserDirectMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \KBox\User
     */
    private $sender;

    /**
     * @var \KBox\User
     */
    private $recipient;

    /**
     * @var string
     */
    private $text;

    /**
     * Create a new direct user message instance.
     *
     * @param User $from the sender of the message
     * @param User $to the recipient of the message
     * @param string $text the content of the message
     * @return \KBox\Mail\UserDirectMessage
     */
    public function __construct(User $from, User $to, $text)
    {
        $this->text = $text;
        $this->sender = $from;
        $this->recipient = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from_mail = Option::mailFrom();
        $from_name = Option::mailFromName();
      
        if (! ends_with($this->sender->email, 'klink.local')) {
            $from_name = $this->sender->name."($from_name)";
        }

        return $this->view('emails.message-html')
                    ->to($this->recipient->email, $this->recipient->name)
                    ->from($from_mail, $from_name)
                    ->replyTo($from_mail, $from_name)
                    ->with(['user' => $this->recipient, 'text' => $this->text, 'sender' => $this->sender->name])
                    ->subject(trans('messaging.mail.subject'));
    }
}
