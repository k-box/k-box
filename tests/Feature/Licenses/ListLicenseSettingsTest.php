<?php

namespace Tests\Feature\Licenses;

use Tests\TestCase;
use KBox\Capability;
use KBox\User;
use KBox\Project;
use KBox\DocumentDescriptor;
use KBox\File;

class ListLicenseSettingsTest extends TestCase
{
    public function test_available_licenses_are_presented()
    {
        $user = User::factory()->create();

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
