<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Option;

class SettingsControllerTest extends TestCase
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
        
        $this->visit( route('administration.settings.index') )
             ->see( trans('administration.menu.settings') )
             ->dontSee( 'administration.menu.settings' );
        
        $this->assertViewHas('pagetitle');
        $this->assertViewHas(Option::MAP_VISUALIZATION_SETTING);
        $this->assertViewHas(Option::PUBLIC_CORE_ENABLED);
        $this->assertViewHas(Option::PUBLIC_CORE_DEBUG);
        $this->assertViewHas(Option::PUBLIC_CORE_URL);
        $this->assertViewHas(Option::PUBLIC_CORE_USERNAME);
        $this->assertViewHas(Option::PUBLIC_CORE_PASSWORD);
        $this->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_EN);
        $this->assertViewHas(Option::PUBLIC_CORE_NETWORK_NAME_RU);
        $this->assertViewHas(Option::SUPPORT_TOKEN);
        $this->assertViewHas(Option::ANALYTICS_TOKEN);
        
    }
    
    
    public function testMapSettingStore(){
        
        
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        // set a false value at the beginning of the test
        Option::put(Option::MAP_VISUALIZATION_SETTING, false);
        
        
        // first: set the value using the form post and see if it is saved
        
        $this->visit( route('administration.settings.index') );
        
        $this->check('map_visualization');
        
        $this->press('map-settings-save-btn');
        
        $this->see( trans('administration.settings.saved') );
        $this->dontSee( 'administration.settings.saved' );

        $this->assertTrue(!!Option::is_map_visualization_enabled(), 'Map visualization has not been enabled');
        $this->assertViewHas(Option::MAP_VISUALIZATION_SETTING, true);
        
        
        
        // second: set a value on the uservoice token and see if map settings is saved
        
        $this->type('Support-token-value', 'support_token');
        
        $this->press(trans('administration.settings.support_save_btn'));
        
        $this->see( trans('administration.settings.saved') );
        $this->dontSee( 'administration.settings.saved' );
        
        
        $this->assertViewHas(Option::SUPPORT_TOKEN, 'Support-token-value');
        
        $this->assertTrue(!!Option::is_map_visualization_enabled(), 'Map visualization has been disabled');
        $this->assertViewHas(Option::MAP_VISUALIZATION_SETTING, true);
        
    }
    
    public function testUservoiceSettingStore(){
        
        
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->visit( route('administration.settings.index') );
        
        $this->type('Support-token-value', 'support_token');
        
        $this->press(trans('administration.settings.support_save_btn'));
        
        $this->see( trans('administration.settings.saved') );
        $this->dontSee( 'administration.settings.saved' );
        
        
        $this->assertViewHas(Option::SUPPORT_TOKEN, 'Support-token-value');
        
        $this->type('', 'support_token');
        
        $this->press(trans('administration.settings.support_save_btn'));
        
        $this->see( trans('administration.settings.saved') );
        $this->dontSee( 'administration.settings.saved' );
        
        
        $this->assertViewHas(Option::SUPPORT_TOKEN, '');

        
    }

    public function testAnalyticsSettingStore(){
        
        
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->visit( route('administration.settings.index') );
        
        $this->type('Analytics-token-value', 'analytics_token');
        
        $this->press(trans('administration.settings.analytics_save_btn'));
        
        $this->see( trans('administration.settings.saved') );
        $this->dontSee( 'administration.settings.saved' );

        $this->assertEquals('Analytics-token-value', analytics_token());
        
        
        $this->assertViewHas(Option::ANALYTICS_TOKEN, 'Analytics-token-value');
        
        $this->type('', 'analytics_token');
        
        $this->press(trans('administration.settings.analytics_save_btn'));
        
        $this->see( trans('administration.settings.saved') );
        $this->dontSee( 'administration.settings.saved' );
        
        
        $this->assertViewHas(Option::ANALYTICS_TOKEN, '');

        
    }
}