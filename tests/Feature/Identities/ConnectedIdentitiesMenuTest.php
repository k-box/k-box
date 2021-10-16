<?php

namespace Tests\Feature\Identities;

use KBox\User;
use Tests\TestCase;

class ConnectedIdentitiesMenuTest extends TestCase
{
    public function test_connected_identities_menu_is_visible()
    {
        config(['identities.providers' => 'gitlab']);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.index'));

        $response->assertStatus(200);

        $response->assertSee(trans('profile.identities'));
    }
    
    public function test_connected_identities_menu_not_visible()
    {
        config(['identities.providers' => null]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.index'));

        $response->assertStatus(200);

        $response->assertDontSee(trans('profile.identities'));
    }
}
