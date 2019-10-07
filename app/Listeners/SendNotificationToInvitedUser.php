<?php

namespace KBox\Listeners;

use Exception;
use KBox\Events\UserInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use KBox\Auth\Registration;
use KBox\Invite;

class SendNotificationToInvitedUser implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  UserInvited  $event
     * @return void
     */
    public function handle(UserInvited $event)
    {
        if (! $event->invite instanceof Invite) {
            return ;
        }

        if (! Registration::isEnabled()) {
            return ;
        }

        if ($event->invite->wasAccepted()) {
            return ;
        }
        
        if (is_null($event->invite->creator)) {
            return ;
        }

        try {
            $event->invite->sendInviteNotification();
        } catch (Exception $ex) {
            $event->invite->markErrored();

            logs()->error('Invite email notification failure', ['invite' => $event->invite->uuid, 'reason' => $ex->getMessage()]);
        }
    }

    public function shouldQueue($event)
    {
        return $event->invite instanceof Invite && ! $event->invite->wasAccepted();
    }
}
