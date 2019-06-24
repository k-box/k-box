<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Option;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingsControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testSettingsIndex()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->get(route('administration.settings.index'));

        $response->assertSee(trans('administration.menu.settings'));
        $response->assertDontSee('administration.menu.settings');
        $response->assertViewHas('pagetitle');
        $response->assertViewHas(Option::PUBLIC_CORE_ENABLED);
        $response->assertViewHas(Option::PUBLIC_CORE_DEBUG);
        $response->assertViewHas(Option::PUBLIC_CORE_URL);
        $response->assertViewHas(Option::PUBLIC_CORE_USERNAME);
        $response->assertViewHas(Option::PUBLIC_CORE_PASSWORD);
        $response->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_EN);
        $response->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_RU);
        $response->assertViewHas(Option::SUPPORT_TOKEN);
        $response->assertViewHas(Option::ANALYTICS_TOKEN);
        $response->assertViewHas(Option::STREAMING_SERVICE_URL);
    }
    
    public function testUservoiceSettingStore()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->from(route('administration.settings.index'))
                         ->post(route('administration.settings.store'), [
                            'support_token' => 'Support-token-value',
                            'support-settings-save-btn' => true, // simulating pressing save on the form
                         ]);
        
        $response->assertRedirect(route('administration.settings.index'));
        $response->assertSessionHas('flash_message', trans('administration.settings.saved'));

        $this->assertEquals('Support-token-value', Option::support_token());

        $saved_get_response = $this->actingAs($user)
                         ->get(route('administration.settings.index'));
        $saved_get_response->assertViewHas(Option::SUPPORT_TOKEN, 'Support-token-value');
        
        $remove_response = $this->actingAs($user)
                         ->from(route('administration.settings.index'))
                         ->post(route('administration.settings.store'), [
                            'support_token' => '',
                            'support-settings-save-btn' => true, // simulating pressing save on the form
                         ]);
        
        $remove_response->assertRedirect(route('administration.settings.index'));
        $remove_response->assertSessionHas('flash_message', trans('administration.settings.saved'));

        $this->assertEquals('', Option::support_token());
        
        $updated_get_response = $this->actingAs($user)
                         ->get(route('administration.settings.index'));
        $updated_get_response->assertViewHas(Option::SUPPORT_TOKEN, '');
    }

    public function test_network_settings_are_stored()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $adapter = $this->withKlinkAdapterMock();
        
        $adapter->shouldReceive('test')->andReturn(['status' => 'ok']);
        
        $response = $this->actingAs($user)
                        ->from(route('administration.settings.index'))
                        ->post(route('administration.settings.store'), [
                            'public_core_url' => 'http://network.local',
                            'public_core_password' => 'A-TOKEN',
                            'public_core_network_name_ru' => 'Test a network',
                            'public_core_network_name_en' => 'Test a network',
                            'public_core_enabled' => 'true',
                            'public-settings-save-btn' => true,
                        ]);

        $response->assertRedirect(route('administration.settings.index'));
        $response->assertSessionHas('flash_message', trans('administration.settings.saved'));
                
        $this->assertEquals(true, Option::option(Option::PUBLIC_CORE_ENABLED));
        $this->assertEquals('http://network.local', Option::option(Option::PUBLIC_CORE_URL));
        $this->assertEquals('', Option::option(Option::PUBLIC_CORE_USERNAME));
        $this->assertEquals('A-TOKEN', base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD)));
        $this->assertEquals('Test a network', Option::option(Option::PUBLIC_CORE_NETWORK_NAME_EN));
        $this->assertEquals('Test a network', Option::option(Option::PUBLIC_CORE_NETWORK_NAME_RU));
    }

    public function test_streaming_service_url_is_stored()
    {
        $adapter = $this->withKlinkAdapterMock();
        
        $adapter->shouldReceive('test')->andReturn(['status' => 'ok']);
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
            ->from(route('administration.settings.index'))
            ->post(route('administration.settings.store'), [
                'streaming_service_url' => 'http://streaming.local',
                'public_core_url' => 'http://network.local',
                'public_core_password' => 'A-TOKEN',
            ]);
        
        $response->assertRedirect(route('administration.settings.index'));
        $response->assertSessionHas('flash_message', trans('administration.settings.saved'));
        
        $this->assertEquals('http://streaming.local', Option::option(Option::STREAMING_SERVICE_URL));
    }
}
