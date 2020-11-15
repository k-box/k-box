<?php

namespace Tests\Feature\Identities;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\User;
use Tests\TestCase;

class ConnectedIdentitiesMenuTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_connected_identities_menu_is_visible()
    {
        config(['identities.providers' => 'gitlab']);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('profile.index'));

        $response->assertStatus(200);

        $response->assertSee(trans('profile.identities'));
    }
    
    public function test_connected_identities_menu_not_visible()
    {
        config(['identities.providers' => null]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('profile.index'));

        $response->assertStatus(200);

        $response->assertDontSee(trans('profile.identities'));
    }
}
