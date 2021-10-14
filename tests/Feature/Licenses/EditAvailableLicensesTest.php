<?php

namespace Tests\Feature\Licenses;

use KBox\Option;
use Tests\TestCase;
use OneOffTech\Licenses\License;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EditAvailableLicensesTest extends TestCase
{
    use  WithoutMiddleware;

    public function test_at_least_one_license_is_selected()
    {
        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/available', [
            'available_licenses' => ''
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors('available_licenses');
        $this->assertFalse(Option::areAvailableLicensesConfigured());
    }
    
    public function test_array_is_required_for_available_license_selection()
    {
        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/available', [
            'available_licenses' => 'a-string'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors('available_licenses');
        $this->assertFalse(Option::areAvailableLicensesConfigured());
    }

    public function test_invalid_license_is_selected()
    {
        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/available', [
            'available_licenses' => ['a-string']
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors();
        $this->assertFalse(Option::areAvailableLicensesConfigured());
    }

    public function test_available_license_option_is_saved()
    {
        Option::put(Option::COPYRIGHT_AVAILABLE_LICENSES, 'null');

        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/available', [
            'available_licenses' => ['C', 'PD', 'CC-BY-4.0']
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');

        $this->assertTrue(Option::areAvailableLicensesConfigured());

        $option = Option::copyright_available_licenses();

        $this->assertEquals(3, $option->count());
        $this->assertContainsOnlyInstancesOf(License::class, $option);
        $this->assertEquals(['C', 'PD', 'CC-BY-4.0'], $option->pluck('id')->toArray());
    }
}
