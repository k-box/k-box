<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Option;

class SettingsControllerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testSettingsIndex()
    {
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->visit(route('administration.settings.index'))
             ->see(trans('administration.menu.settings'))
             ->dontSee('administration.menu.settings');
        
        $this->assertViewHas('pagetitle');
        $this->assertViewHas(Option::PUBLIC_CORE_ENABLED);
        $this->assertViewHas(Option::PUBLIC_CORE_DEBUG);
        $this->assertViewHas(Option::PUBLIC_CORE_URL);
        $this->assertViewHas(Option::PUBLIC_CORE_USERNAME);
        $this->assertViewHas(Option::PUBLIC_CORE_PASSWORD);
        $this->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_EN);
        $this->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_RU);
        $this->assertViewHas(Option::SUPPORT_TOKEN);
        $this->assertViewHas(Option::ANALYTICS_TOKEN);
        $this->assertViewHas(Option::STREAMING_SERVICE_URL);
    }
    
    public function testUservoiceSettingStore()
    {
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->visit(route('administration.settings.index'));
        
        $this->type('Support-token-value', 'support_token');
        
        $this->press(trans('administration.settings.support_save_btn'));
        
        $this->see(trans('administration.settings.saved'));
        $this->dontSee('administration.settings.saved');
        
        $this->assertViewHas(Option::SUPPORT_TOKEN, 'Support-token-value');
        
        $this->type('', 'support_token');
        
        $this->press(trans('administration.settings.support_save_btn'));
        
        $this->see(trans('administration.settings.saved'));
        $this->dontSee('administration.settings.saved');
        
        
        $this->assertViewHas(Option::SUPPORT_TOKEN, '');
    }

    public function testAnalyticsSettingStore()
    {
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->visit(route('administration.settings.index'));
        
        $this->type('Analytics-token-value', 'analytics_token');
        
        $this->press(trans('administration.settings.analytics_save_btn'));
        
        $this->see(trans('administration.settings.saved'));
        $this->dontSee('administration.settings.saved');

        $this->assertEquals('Analytics-token-value', analytics_token());
        
        
        $this->assertViewHas(Option::ANALYTICS_TOKEN, 'Analytics-token-value');
        
        $this->type('', 'analytics_token');
        
        $this->press(trans('administration.settings.analytics_save_btn'));
        
        $this->see(trans('administration.settings.saved'));
        $this->dontSee('administration.settings.saved');
        
        
        $this->assertViewHas(Option::ANALYTICS_TOKEN, '');
    }

    public function test_network_settings_are_stored()
    {
        $user = $this->createAdminUser();

        $adapter = $this->withKlinkAdapterMock();
        
        $adapter->shouldReceive('test')->andReturn(['status' => 'ok']);
        
        $this->actingAs($user);
        
        $this->visit(route('administration.settings.index'));
        
        $this->type('http://network.local', 'public_core_url');
        $this->type('A-TOKEN', 'public_core_password');
        $this->type('Test a network', 'public_core_network_name_ru');
        $this->type('Test a network', 'public_core_network_name_en');
        $this->check('public_core_enabled');
        
        $this->press(trans('administration.settings.save_btn'));
        
        $this->see(trans('administration.settings.saved'));
        $this->dontSee('administration.settings.saved');
                
        $this->assertViewHas(Option::PUBLIC_CORE_ENABLED, true);
        $this->assertViewHas(Option::PUBLIC_CORE_URL, 'http://network.local');
        $this->assertViewHas(Option::PUBLIC_CORE_USERNAME, '');
        $this->assertViewHas(Option::PUBLIC_CORE_PASSWORD, 'A-TOKEN');
        $this->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_EN, 'Test a network');
        $this->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_RU, 'Test a network');
    }

    public function test_streaming_service_url_is_stored()
    {
        $adapter = $this->withKlinkAdapterMock();
        
        $adapter->shouldReceive('test')->andReturn(['status' => 'ok']);
        
        $user = $this->createAdminUser();
        $this->actingAs($user);
        $this->visit(route('administration.settings.index'));

        $this->assertViewHas(Option::STREAMING_SERVICE_URL, '');
        
        $this->type('http://streaming.local', 'streaming_service_url');
        $this->type('http://network.local', 'public_core_url');
        $this->type('A-TOKEN', 'public_core_password');

        $this->press(trans('administration.settings.save_btn'));
        
        $this->see(trans('administration.settings.saved'));
        $this->dontSee('administration.settings.saved');
        
        $this->assertViewHas(Option::STREAMING_SERVICE_URL, 'http://streaming.local');
    }
}
