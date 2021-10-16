<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Consent;
use KBox\Option;
use KBox\Consents;
use KBox\Support\Analytics\Analytics;

class AnalyticsTest extends TestCase
{
    public function test_tracking_not_included_for_guest_users()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('<!-- Analytics Code -->');
    }
    
    public function test_tracking_not_included_without_consent()
    {
        config([
            'analytics.token' => 'aaaa'
        ]);

        $user = User::factory()->admin()->create();
        Consent::withdraw($user, Consents::STATISTIC);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('<!-- Analytics Code -->');
    }

    public function test_tracking_included_when_logged_in()
    {
        config([
            'analytics.token' => 'aaaa'
        ]);

        $user = User::factory()->admin()->create();
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('<!-- Analytics Code -->', false);
    }

    public function test_matomo_tracking_using_environment_configuration()
    {
        config([
            'analytics.token' => '1',
            'analytics.services.matomo.domain' => 'https://example.analytics',
        ]);

        $user = User::factory()->admin()->create();
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('https://example.analytics', false);
        $response->assertSee('<!-- Matomo -->', false);

        $this->assertEquals('1', Analytics::token());
        $this->assertEquals('analytics.matomo', Analytics::view());
        $this->assertEquals(['token' => '1', 'domain' => 'https://example.analytics'], Analytics::configuration());
    }

    public function test_matomo_not_used_if_domain_is_empty()
    {
        config([
            'analytics.token' => '1',
            'analytics.services.matomo.domain' => '',
        ]);

        $user = User::factory()->admin()->create();
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertDontSee('<!-- Matomo -->');
    }

    public function test_google_analytics_tracking_using_environment_configuration()
    {
        config([
            'analytics.token' => '1',
            'analytics.service' => 'google-analytics',
        ]);

        $user = User::factory()->admin()->create();
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('<!-- Google Analytics -->', false);

        $this->assertEquals('1', Analytics::token());
        $this->assertEquals('analytics.google-analytics', Analytics::view());
        $this->assertEquals(['token' => '1'], Analytics::configuration());
    }

    public function test_analytics_settings_page_loads_env_variables()
    {
        config([
            'analytics.token' => '1',
            'analytics.services.matomo.domain' => 'https://example.analytics',
        ]);

        $user = User::factory()->admin()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('administration.analytics.index'));
        
        $response->assertViewIs('administration.analytics.index');
        $response->assertViewHas(Analytics::ANALYTICS_TOKEN, '1');
        $response->assertViewHas('analytics_domain', 'https://example.analytics');
        $response->assertViewHas('analytics_service', 'matomo');
        $response->assertViewHas('available_services', ['matomo', 'google-analytics']);
    }

    public function test_analytics_settings_page_loads_dynamic_settings()
    {
        config([
            'analytics.services.matomo.domain' => 'https://example.analytics',
        ]);

        Option::put(Analytics::ANALYTICS_TOKEN, 'aaaa');

        $user = User::factory()->admin()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('administration.analytics.index'));
        
        $response->assertViewIs('administration.analytics.index');
        $response->assertViewHas(Analytics::ANALYTICS_TOKEN, 'aaaa');
        $response->assertViewHas('analytics_domain', 'https://example.analytics');
        $response->assertViewHas('analytics_service', 'matomo');
        $response->assertViewHas('available_services', ['matomo', 'google-analytics']);
    }

    public function test_analytics_setting_are_saved()
    {
        $user = User::factory()->admin()->create();
        
        $response = $this->actingAs($user)
                         ->from(route('administration.analytics.index'))
                         ->put(route('administration.analytics.update'), [
                            'analytics_token' => 'Analytics-token-value',
                            'analytics_domain' => 'example.analytics',
                         ]);
        
        $response->assertRedirect(route('administration.analytics.index'));
        $response->assertSessionHas('flash_message', trans('administration.analytics.saved'));

        $this->assertEquals('Analytics-token-value', analytics_token());
        $this->assertEquals('https://example.analytics/', Analytics::configuration('domain'));
        
        $response = $this->actingAs($user)
                         ->from(route('administration.analytics.index'))
                         ->put(route('administration.analytics.update'), [
                            'analytics_token' => '',
                         ]);
        
        $response->assertRedirect(route('administration.analytics.index'));
        $response->assertSessionHas('flash_message', trans('administration.analytics.saved'));

        $this->assertEquals('', analytics_token());
    }
    
    public function test_analytics_domain_https_is_handled()
    {
        $user = User::factory()->admin()->create();
        
        $response = $this->actingAs($user)
                         ->from(route('administration.analytics.index'))
                         ->put(route('administration.analytics.update'), [
                            'analytics_token' => 'Analytics-token-value',
                            'analytics_domain' => 'https://example.analytics/',
                         ]);
        
        $response->assertRedirect(route('administration.analytics.index'));
        $response->assertSessionHas('flash_message', trans('administration.analytics.saved'));

        $this->assertEquals('Analytics-token-value', analytics_token());
        $this->assertEquals('https://example.analytics/', Analytics::configuration('domain'));
    }
}
