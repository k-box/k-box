<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Invite;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\Notifications\InviteEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\Messages\MailMessage;

class InviteEmailTest extends TestCase
{
    use WithFaker;
    
    public function test_generic_invite_mail_message()
    {
        $creator_name = 'John';

        $user = tap(User::factory()->create([
            'name' => $creator_name,
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'creator_id' => $user->id,
        ]);

        $mail = (new InviteEmail())->toMail($invite);

        $expected_url = URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]);

        $this->assertEquals(trans('invite.notification.mail.subject', ['name' => $creator_name]), $mail->subject);
        $this->assertEquals(trans('invite.notification.mail.greeting', ['name' => $creator_name, 'url' => url('/')]), $mail->greeting);
        $this->assertEquals(trans('messaging.mail.do_not_reply'), $mail->salutation);
        $this->assertEquals(trans('auth.create_account'), $mail->actionText);
        $this->assertEquals($expected_url, $mail->actionUrl);
        $this->assertEquals([trans('invite.notification.mail.reason.invitation', ['name' => $creator_name, 'url' => url('/')])], $mail->introLines);
        $this->assertEquals([trans('invite.notification.mail.no_further_action', ['date' => $invite->expire_at->toDateString()])], $mail->outroLines);
    }

    public function test_project_invite_mail_message()
    {
        $creator_name = 'John';

        $user = tap(User::factory()->create([
            'name' => $creator_name,
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $invite = factory(Invite::class)->create([
            'creator_id' => $user->id,
            'actionable_id' => $project->id,
            'actionable_type' => get_class($project),
        ]);

        $mail = (new InviteEmail())->toMail($invite);

        $expected_url = URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]);

        $this->assertEquals(trans('invite.notification.mail.subject', ['name' => $creator_name]), $mail->subject);
        $this->assertEquals(trans('invite.notification.mail.greeting', ['name' => $creator_name, 'url' => url('/')]), $mail->greeting);
        $this->assertEquals(trans('messaging.mail.do_not_reply'), $mail->salutation);
        $this->assertEquals(trans('auth.create_account'), $mail->actionText);
        $this->assertEquals($expected_url, $mail->actionUrl);
        $this->assertEquals([trans('invite.notification.mail.reason.project', ['name' => $creator_name, 'url' => url('/')])], $mail->introLines);
        $this->assertEquals([trans('invite.notification.mail.no_further_action', ['date' => $invite->expire_at->toDateString()])], $mail->outroLines);
    }

    public function test_mail_message_generated_using_custom_callback()
    {
        $creator_name = 'John';

        $user = tap(User::factory()->create([
            'name' => $creator_name,
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'creator_id' => $user->id,
        ]);

        InviteEmail::toMailUsing(function ($notifiable) {
            return (new MailMessage)->subject('custom');
        });

        $mail = (new InviteEmail())->toMail($invite);

        $expected_url = URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]);

        $this->assertEquals('custom', $mail->subject);
        $this->assertNull($mail->greeting);
        $this->assertNull($mail->salutation);
        $this->assertNull($mail->actionText);
        $this->assertNull($mail->actionUrl);
        $this->assertEmpty($mail->introLines);
        $this->assertEmpty($mail->outroLines);
    }
}
