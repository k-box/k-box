<?php

namespace Tests\Unit;

use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
    use DatabaseTransactions;

    public function test_invite_is_generated()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

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
        $this->assertNull($invite->accepted_at);

        Event::assertDispatched(UserInvited::class, function ($e) use ($invite) {
            return $e->invite->is($invite);
        });
    }
    
    public function test_invite_actionable_is_stored()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(Project::class)->create([
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
        $user = tap(factory(User::class)->create(['email_verified_at' => null]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $email = $this->faker->safeEmail;

        $this->expectException(AuthorizationException::class);

        $invite = Invite::generate($user, $email);

        Event::assertNotDispatched(UserInvited::class);
    }

    public function test_invite_is_accepted()
    {
        $invite = factory(Invite::class)->create();
        
        $user = tap(factory(User::class)->create(['email' => $invite->email]), function ($u) {
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
}
