<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use KBox\Capability;
use KBox\Events\UserInvited;
use KBox\Notifications\InviteEmail;
use KBox\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use KBox\Invite;

class SendInviteTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_invite_notification_sent()
    {
        $creator = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox'
        ]);
        
        Notification::fake();

        $invite->sendInviteNotification();

        $this->assertArrayHasKey('notified_at', $invite->fresh()->details);

        Notification::assertSentTo(
            $invite,
            InviteEmail::class
        );
        Notification::assertNotSentTo(
            $creator,
            InviteEmail::class
        );
    }

    public function test_invite_notification_not_sent_if_already_accepted()
    {
        $creator = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox',
            'accepted_at' => now(),
            'user_id' => $creator->getKey(),
        ]);
        
        Notification::fake();

        $invite->sendInviteNotification();

        Notification::assertNotSentTo(
            $invite,
            InviteEmail::class
        );
        Notification::assertNotSentTo(
            $creator,
            InviteEmail::class
        );
    }

    public function test_invite_notification_not_sent_if_expired()
    {
        $creator = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox',
            'expire_at' => now()->subDays(1),
            'user_id' => $creator->getKey(),
        ]);
        
        Notification::fake();

        $invite->sendInviteNotification();

        Notification::assertNotSentTo(
            $invite,
            InviteEmail::class
        );
        Notification::assertNotSentTo(
            $creator,
            InviteEmail::class
        );
    }

    public function test_invite_notification_not_sent_if_user_registration_not_active()
    {
        config([
            'dms.registration' => false,
        ]);

        $creator = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox',
            'accepted_at' => now(),
            'user_id' => $creator->getKey(),
        ]);
        
        Notification::fake();

        $invite->sendInviteNotification();

        Notification::assertNotSentTo(
            $invite,
            InviteEmail::class
        );
        Notification::assertNotSentTo(
            $creator,
            InviteEmail::class
        );
    }

    public function test_user_invited_listener_send_notification()
    {
        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox'
        ]);

        Notification::fake();

        event(new UserInvited($invite));

        Notification::assertSentTo(
            $invite,
            InviteEmail::class
        );
    }

    public function test_user_invited_listener_do_nothing_if_invite_already_accepted()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox',
            'accepted_at' => now(),
            'user_id' => $user->getKey(),
        ]);

        Notification::fake();

        event(new UserInvited($invite));

        Notification::assertNotSentTo(
            $invite,
            InviteEmail::class
        );
    }

    public function test_user_invited_listener_do_nothing_if_user_registration_disabled()
    {
        config([
            'dms.registration' => false,
        ]);

        Config::set('dms.registration', false);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox',
            'accepted_at' => now(),
            'user_id' => $user->getKey(),
        ]);

        Notification::fake();

        event(new UserInvited($invite));

        Notification::assertNotSentTo(
            $invite,
            InviteEmail::class
        );
    }

    public function test_user_invited_listener_do_nothing_if_invite_creator_is_disabled()
    {
        $invite = factory(Invite::class)->create([
            'email' => 'john@kbox.kbox',
        ]);

        $invite->creator->delete();

        Notification::fake();

        event(new UserInvited($invite));

        Notification::assertNotSentTo(
            $invite,
            InviteEmail::class
        );
    }
}
