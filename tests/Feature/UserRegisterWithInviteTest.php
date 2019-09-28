<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
use KBox\Capability;
use KBox\Invite;
use KBox\User;

class UserRegisterWithInviteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_invite_info_presented_on_registration_form()
    {
        $invite = factory(Invite::class)->create();

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]));

        $response->assertOk();
        $response->assertViewIs('auth.register');
        $response->assertViewHas('invite', $invite->token);
        $response->assertViewHas('email', $invite->email);
        $response->assertSee($invite->email);
        $response->assertSee($invite->token);
        $response->assertDontSee($invite->uuid);
    }
    
    public function test_invite_denied_if_expired()
    {
        $invite = factory(Invite::class)->create([
            'expire_at' => now(),
        ]);

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]));

        $response->assertOk();
        $response->assertViewIs('auth.register');
        $response->assertViewHas('invite_error', trans('invite.invalid'));
        $response->assertViewMissing('invite');
        $response->assertViewMissing('email');
        $response->assertDontSee($invite->email);
        $response->assertDontSee($invite->token);
        $response->assertDontSee($invite->uuid);
    }
    
    public function test_invite_denied_if_already_accepted()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'accepted_at' => now(),
            'user_id' => $user->id,
        ]);

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]));

        $response->assertOk();
        $response->assertViewIs('auth.register');
        $response->assertViewHas('invite_error', trans('invite.invalid'));
        $response->assertViewMissing('invite');
        $response->assertViewMissing('email');
        $response->assertDontSee($invite->email);
        $response->assertDontSee($invite->token);
        $response->assertDontSee($invite->uuid);
    }
    
    public function test_invite_denied_if_deleted()
    {
        $invite = factory(Invite::class)->create();

        $invite->delete();

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid,
            'e' => $invite->email,
        ]));

        $response->assertOk();
        $response->assertViewIs('auth.register');
        $response->assertViewHas('invite_error', trans('invite.invalid'));
        $response->assertViewMissing('invite');
        $response->assertViewMissing('token');
        $response->assertViewMissing('email');
        $response->assertDontSee($invite->email);
        $response->assertDontSee($invite->token);
        $response->assertDontSee($invite->uuid);
    }
    
    public function test_invite_denied_if_signature_cannot_be_verified()
    {
        $invite = factory(Invite::class)->create();

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid.'in',
            'e' => $invite->email,
        ]));

        $response->assertOk();
        $response->assertViewIs('auth.register');
        $response->assertViewHas('invite_error', trans('invite.invalid'));
        $response->assertViewMissing('invite');
        $response->assertViewMissing('token');
        $response->assertViewMissing('email');
        $response->assertDontSee($invite->email);
        $response->assertDontSee($invite->token);
        $response->assertDontSee($invite->uuid);
    }
    
    public function test_user_connected_to_invite_after_registration()
    {
        $invite = factory(Invite::class)->create();

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid.'in',
            'e' => $invite->email,
        ]));

        $response = $this->post(route('register'), [
            'email' => $invite->email,
            'password' => 'a-long-and-secure-password',
            'password_confirmation' => 'a-long-and-secure-password',
            'invite' => $invite->token,
        ]);

        $response->assertRedirect(url('/'));

        $user = User::where('email', $invite->email)->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasVerifiedEmail());

        $refreshed_invite = $invite->fresh();

        $this->assertTrue($refreshed_invite->wasAccepted());
        $this->assertEquals($user->id, $refreshed_invite->user_id);
    }
    
    public function test_user_can_use_a_different_email_address()
    {
        $invite = factory(Invite::class)->create();

        $response = $this->get(URL::signedRoute('register', [
            'i' => $invite->uuid.'in',
            'e' => $invite->email,
        ]));

        $different_email = 'john@kbox.kbox';

        $response = $this->post(route('register'), [
            'email' => $different_email,
            'password' => 'a-long-and-secure-password',
            'password_confirmation' => 'a-long-and-secure-password',
            'invite' => $invite->token,
        ]);

        $response->assertRedirect('/email/verify');

        $user = User::where('email', $different_email)->first();

        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());

        $refreshed_invite = $invite->fresh();

        $this->assertTrue($refreshed_invite->wasAccepted());
        $this->assertEquals($user->id, $refreshed_invite->user_id);
    }
}
