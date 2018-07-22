<?php

namespace Tests\Feature\Licenses;

use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListLicenseSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_available_licenses_are_presented()
    {
        $user = factory(\KBox\User::class)->create();

        $user->addCapabilities(Capability::$ADMIN);

        $response = $this->actingAs($user)->get('/administration/licenses');

        $response->assertStatus(200);
        
        $response->assertViewIs('administration.documentlicenses.index');
        $response->assertViewHas('pagetitle');
        $response->assertViewHas('licenses');
        $response->assertViewHas('selected_licenses');
        $response->assertViewHas('default_license');
        $response->assertViewHas('settings_are_explicitly_configured');
    }
}
