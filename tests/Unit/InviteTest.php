<?php

namespace Tests\Unit;

use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Support\Facades\Event;
use KBox\Capability;
use KBox\Events\UserInviteAccepted;
use KBox\Events\UserInvited;
use KBox\Invite;
use KBox\Project;
use KBox\User;

class InviteTest extends TestCase
{
    use WithFaker;
    
    public function test_invite_is_generated()
    {
        $user = User::factory()->partner()->create();

        Event::fake([
            UserInvited::class
        ]);

        $email = $this->faker->safeEmail;
        $invite = Invite::generate($user, $email);

        $this->assertNotNull($invite);
        $this->assertInstanceOf(Invite::class, $invite);
        $this->assertTrue($invite->creator->is($user));
        $this->assertEquals($email, $invite->email);
        $this->assertNotNull($invite->token);
        $this->assertNotNull($invite->expire_at);
        $this->assertNull($invite->accepted_at);

        Event::assertDispatched(UserInvited::class, function ($e) use ($invite) {
            return $e->invite->is($invite);
        });
    }
    
    public function test_invite_actionable_is_stored()
    {
        $user = User::factory()->partner()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        Event::fake([
            UserInvited::class
        ]);

        $email = $this->faker->safeEmail;
        $invite = Invite::generate($user, $email, $project);

        $this->assertNotNull($invite);
        $this->assertInstanceOf(Invite::class, $invite);
        $this->assertTrue($invite->creator->is($user));
        $this->assertEquals($email, $invite->email);
        $this->assertNotNull($invite->actionable_id);
        $this->assertNotNull($invite->actionable_type);
        $this->assertTrue($invite->actionable->is($project));

        Event::assertDispatched(UserInvited::class, function ($e) use ($invite) {
            return $e->invite->is($invite);
        });
    }

    public function test_invite_generation_denied_if_user_dont_have_verified_email()
    {
        $user = tap(User::factory()->create(['email_verified_at' => null]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $email = $this->faker->safeEmail;

        $this->expectException(AuthorizationException::class);

        $invite = Invite::generate($user, $email);

        Event::assertNotDispatched(UserInvited::class);
    }

    public function test_invite_is_accepted()
    {
        $invite = Invite::factory()->create();
        
        $user = tap(User::factory()->create(['email' => $invite->email]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Event::fake([
            UserInviteAccepted::class
        ]);

        $accepted_invite = $invite->accept($user);

        $this->assertTrue($accepted_invite->wasAccepted());
        $this->assertNotNull($accepted_invite->accepted_at);
        $this->assertEquals($user->id, $accepted_invite->user_id);

        Event::assertDispatched(UserInviteAccepted::class, function ($e) use ($accepted_invite) {
            return $e->invite->is($accepted_invite);
        });
    }

    public function test_invite_can_be_marked_as_notified()
    {
        $invite = Invite::factory()->create();

        $notified_invite = $invite->markNotified();

        $this->assertArrayHasKey('notified_at', $notified_invite->details);
    }

    public function test_invite_can_be_marked_as_errored()
    {
        $invite = Invite::factory()->create();

        $errored_invite = $invite->markErrored();

        $this->assertArrayHasKey('errored_at', $errored_invite->details);
    }

    public function test_invite_is_expired()
    {
        $invite = Invite::factory()->create([
            'expire_at' => now()->subDays(config('invite.expiration') + 1)
        ]);

        $this->assertTrue($invite->isExpired());
    }
    
    public function test_expired_invites_can_be_retrieved()
    {
        $valid_invites = Invite::factory()->count(2)->create();
        $expired_invites = Invite::factory()->count(2)->create([
            'expire_at' => now()->subDays(config('invite.expiration') + 1)
        ]);

        $expired = Invite::expired()->get();

        $this->assertEquals(2, $expired->count());
    }
}
