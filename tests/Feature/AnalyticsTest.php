<?php

namespace Tests\Feature;

use KBox\File;
use KBox\User;
use KBox\Project;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\Consent;
use KBox\Consents;
use KBox\Publication;
use KBox\DocumentDescriptor;
use KBox\Support\Analytics\Analytics;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnalyticsTest extends TestCase
{
    use DatabaseTransactions;

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

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
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

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('<!-- Analytics Code -->');
    }

    public function test_matomo_tracking_using_environment_configuration()
    {
        config([
            'analytics.token' => '1',
            'analytics.services.matomo.domain' => 'https://example.analytics',
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('https://example.analytics');
        $response->assertSee('<!-- Matomo -->');

        $this->assertEquals('1', Analytics::token());
        $this->assertEquals('analytics.matomo', Analytics::view());
        $this->assertEquals(['token' => '1', 'domain' => 'https://example.analytics'], Analytics::configuration());
    }

    public function test_google_analytics_tracking_using_environment_configuration()
    {
        config([
            'analytics.token' => '1',
            'analytics.service' => 'google-analytics',
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('<!-- Google Analytics -->');

        $this->assertEquals('1', Analytics::token());
        $this->assertEquals('analytics.google-analytics', Analytics::view());
        $this->assertEquals(['token' => '1'], Analytics::configuration());
    }


    public function test_analytics_setting_are_saved()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)
                         ->from(route('administration.settings.index'))
                         ->post(route('administration.settings.store'), [
                            'analytics_token' => 'Analytics-token-value',
                            'analytics-settings-save-btn' => true, // simulating pressing the button
                         ]);
        
        $response->assertRedirect(route('administration.settings.index'));
        $response->assertSessionHas('flash_message', trans('administration.settings.saved'));

        $this->assertEquals('Analytics-token-value', analytics_token());
        
        $response = $this->actingAs($user)
                         ->from(route('administration.settings.index'))
                         ->post(route('administration.settings.store'), [
                            'analytics_token' => '',
                            'analytics-settings-save-btn' => true, // simulating pressing the button
                         ]);
        
        $response->assertRedirect(route('administration.settings.index'));
        $response->assertSessionHas('flash_message', trans('administration.settings.saved'));

        $this->assertEquals('', analytics_token());
    }
    
}
