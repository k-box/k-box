<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\Option;
use KBox\Support\SupportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_support_not_active()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('UserVoice');
    }
    
    public function test_support_not_active_if_partially_configured()
    {
        config([
            'support.service' => 'uservoice',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('UserVoice');
    }
    
    public function test_uservoice_included_from_environment()
    {
        config([
            'support.service' => 'uservoice',
            'support.providers.uservoice.token' => 'AAAAA',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('UserVoice');
        $response->assertSee('AAAAA.js');
    }

    public function test_uservoice_included_using_dynamic_configuration()
    {
        config([
            'support.service' => null,
            'support.providers.uservoice.token' => null,
        ]);

        Option::put(SupportService::SUPPORT_TOKEN, 'AAAAA');
        Option::put(SupportService::SUPPORT_SERVICE, 'uservoice');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('UserVoice');
        $response->assertSee('AAAAA.js');
    }

    public function test_user_is_passed_to_uservoice_widget()
    {
        config([
            'support.service' => 'uservoice',
            'support.providers.uservoice.token' => 'AAAAA',
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        
        $response->assertSee("UserVoice.push(['identify'", false);
        $response->assertSee("email: '$user->email'", false);
        $response->assertSee("name: '$user->name'", false);
    }

    public function test_support_settings_page_loads_env_variables()
    {
        config([
            'support.service' => 'uservoice',
            'support.providers.uservoice.token' => 'AAAAA',
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->get(route('administration.support.index'));
        
        $response->assertViewIs('administration.support.index');
        $response->assertViewHas(SupportService::SUPPORT_TOKEN, 'AAAAA');
        $this->assertTrue(SupportService::active('uservoice'));
    }

    public function test_support_settings_page_loads_dynamic_settings()
    {
        config([
            'support.service' => 'uservoice',
        ]);

        Option::put(SupportService::SUPPORT_TOKEN, 'AAAAA');

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->get(route('administration.support.index'));
        
        $response->assertViewIs('administration.support.index');
        $response->assertViewHas(SupportService::SUPPORT_TOKEN, 'AAAAA');
    }

    public function test_support_setting_are_saved()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->from(route('administration.support.index'))
                         ->put(route('administration.support.update'), [
                            'support_token' => 'support-token-value',
                         ]);
        
        $response->assertRedirect(route('administration.support.index'));
        $response->assertSessionHas('flash_message', trans('administration.support.saved'));

        $this->assertEquals('support-token-value', support_token());
        $this->assertEquals('uservoice', SupportService::serviceName());
        $this->assertTrue(support_active('uservoice'));
        
        $response = $this->actingAs($user)
                         ->from(route('administration.support.index'))
                         ->put(route('administration.support.update'), [
                            'support_token' => '',
                         ]);
        
        $response->assertRedirect(route('administration.support.index'));
        $response->assertSessionHas('flash_message', trans('administration.support.saved'));

        $this->assertEquals(null, support_token());
        $this->assertEquals(null, SupportService::serviceName());
        $this->assertFalse(support_active('uservoice'));
    }

    public function test_mail_link_support_is_active()
    {
        config([
            'support.service' => 'mail',
            'support.providers.mail.address' => 'k-box@k-box.local',
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->get(route('administration.support.index'));
        
        $this->assertTrue(SupportService::active('mail'));
        $response->assertViewIs('administration.support.index');
        $response->assertSee('k-box@k-box.local');
        $response->assertSee(trans('actions.contact_support'));
    }
}
