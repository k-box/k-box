<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Identity;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserIdentitiesTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_identities_page_require_authentication()
    {
        $response = $this->get(route('profile.identities.index'));

        $response->assertRedirect(url('/'));
    }

    public function test_identities_page_shows_registered_identities()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identities = factory(Identity::class, 2)->create([
            'user_id' => $user->getKey()
        ]);

        $response = $this->actingAs($user)->get(route('profile.identities.index'));

        $response->assertStatus(200);

        $response->assertViewHas('identities', function ($value) use ($identities) {
            return $value->diff($identities)->isEmpty();
        });
        $response->assertSee(trans('identities.connected_at'));
    }

    public function test_identities_page_shows_the_identity_used_for_registration()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identity = factory(Identity::class)->state('registration')->create([
            'user_id' => $user->getKey()
        ]);

        $response = $this->actingAs($user)->get(route('profile.identities.index'));

        $response->assertStatus(200);

        $response->assertViewHas('identities', function ($value) use ($identity) {
            return $value->first()->is($identity);
        });
        $response->assertSee(trans('identities.registration'));
    }

    public function test_identities_page_shows_only_my_identities()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identities = factory(Identity::class, 3)->create();

        $response = $this->actingAs($user)->get(route('profile.identities.index'));

        $response->assertStatus(200);

        $response->assertViewHas('identities', Collection::make());
        $response->assertDontSee(trans('identities.connected_at'));
        $response->assertDontSee(trans('identities.registration'));
    }

    public function test_identity_can_be_removed()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identity = factory(Identity::class)->create([
            'user_id' => $user->getKey()
        ]);

        $response = $this->actingAs($user)->delete(route('profile.identities.destroy', ['identity' => $identity->getKey()]));

        $response->assertRedirect(route('profile.identities.index'));
        $response->assertSessionHas('flash_message', trans('identities.removed', ['provider' => $identity->provider]));

        $this->assertNull($identity->fresh());
    }

    public function test_identity_can_be_removed_via_ajax()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identity = factory(Identity::class)->create([
            'user_id' => $user->getKey()
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('profile.identities.destroy', ['identity' => $identity->getKey()]));

        $response->assertOk();
        $response->assertJson($identity->toArray());

        $this->assertNull($identity->fresh());
    }

    public function test_someone_else_identity_cannot_be_removed()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $identity = factory(Identity::class)->create();

        $response = $this->actingAs($user)->delete(route('profile.identities.destroy', ['identity' => $identity->getKey()]));

        $response->assertForbidden();
    }
}
