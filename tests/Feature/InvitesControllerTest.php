<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use KBox\Capability;
use KBox\Invite;
use KBox\User;

class InvitesControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_invites_are_listed()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $other_invites = factory(Invite::class, 2)->create();
        $my_invites = factory(Invite::class, 2)->create([
            'creator_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('profile.invite.index'));

        $response->assertOk();

        $response->assertViewIs('invites.index');
        $response->assertViewHas('invites');
        $response->assertViewHas('expiration_period', config('invites.expiration'));

        $retrieved_invites = $response->getData('invites');

        $my_invites->each(function ($invite) use ($retrieved_invites) {
            $this->assertTrue($retrieved_invites->contains($invite));
        });
        
        $other_invites->each(function ($invite) use ($retrieved_invites) {
            $this->assertFalse($retrieved_invites->contains($invite));
        });
    }
    
    public function test_user_can_create_invite()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $response = $this->actingAs($user)
            ->from(route('profile.invite.create'))
            ->post(route('profile.invite.store', [
                'email' => 'john@kbox.kbox'
            ]));

        $response->assertRedirect(route('profile.invite.index'));
        
        $invite = Invite::mine($user)->where('email', 'john@kbox.kbox')->first();

        $this->assertNotNull($invite);

        $response->assertSessionHas('flash_message', trans('invite.created', ['email' => $invite->email]));
    }
    
    public function test_invite_create_page_loads()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $response = $this->actingAs($user)
            ->get(route('profile.invite.create'));

        $response->assertOk();
        $response->assertViewIs('invites.create');
    }
    
    public function test_user_can_invite_same_person_once()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'creator_id' => $user->id,
            'email' => 'john@kbox.kbox',
        ]);

        $response = $this->actingAs($user)
            ->from(route('profile.invite.create'))
            ->post(route('profile.invite.store', [
                'email' => 'john@kbox.kbox'
            ]));

        $response->assertRedirect(route('profile.invite.create'));

        $response->assertSessionHasErrors('email');

        $this->assertEquals(1, Invite::mine($user)->where('email', 'john@kbox.kbox')->count());
    }
    
    public function test_user_can_delete_own_invite()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'creator_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->from(route('profile.invite.index'))
            ->delete(route('profile.invite.destroy', [
                'invite' => $invite->uuid
            ]));

        $response->assertRedirect(route('profile.invite.index'));

        $response->assertSessionHas('flash_message', trans('invite.deleted', ['email' => $invite->email]));
        
        $this->assertNull($invite->fresh());
    }
    
    public function test_user_cannot_delete_others_invite()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create();

        $response = $this->actingAs($user)
            ->from(route('profile.invite.index'))
            ->delete(route('profile.invite.destroy', [
                'invite' => $invite->uuid
            ]));

        $response->assertForbidden();
        
        $this->assertNotNull($invite->fresh());
    }
    
    public function test_invite_listing_requires_authentication()
    {
        $response = $this->get(route('profile.invite.index'));

        $response->assertRedirect('/');
    }
    
    public function test_invite_routes_require_registration_to_be_active()
    {
        Config::set('dms.registration', false);

        $response = $this->get(route('profile.invite.index'));

        $response->assertStatus(404);
    }

    public function test_create_invite_button_visible()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $other_invites = factory(Invite::class, 2)->create();
        $my_invites = factory(Invite::class, 2)->create([
            'creator_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('profile.invite.index'));

        $response->assertOk();

        $response->assertViewIs('invites.index');
        $response->assertViewHas('invites');
        $response->assertSee(__('invite.create.title'));
    }
}
