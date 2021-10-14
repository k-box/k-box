<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Option;
use Tests\TestCase;
use KBox\Capability;

class KlinkGuestSearchTest extends TestCase
{
    public function test_search_not_presented_on_login_page_if_guest_search_disabled()
    {
        config(['dms.are_guest_public_search_enabled' => false]);
        
        $response = $this->get('/');

        $response->assertViewMissing('show_search');
        $response->assertDontSee('name="s"');
    }

    public function test_search_page_forbidden_if_guest_search_disabled()
    {
        config(['dms.are_guest_public_search_enabled' => false]);
        
        $response = $this->get('/search');

        $response->assertStatus(403);
    }

    public function test_search_not_presented_on_login_page_if_klink_not_configured()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, false);
        config(['dms.are_guest_public_search_enabled' => true]);
        
        $response = $this->get('/');

        $response->assertViewMissing('show_search');
        $response->assertDontSee('name="s"');
    }

    public function test_search_not_presented_on_login_page_when_klink_enabled()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
        config(['dms.are_guest_public_search_enabled' => false]);
        
        $response = $this->get('/');

        $response->assertViewMissing('show_search');
        $response->assertDontSee('name="s"');
    }

    public function test_search_page_forbidden_if_klink_not_configured()
    {
        Option::option(Option::PUBLIC_CORE_ENABLED, false);
        config(['dms.are_guest_public_search_enabled' => true]);
        
        $response = $this->get('/search');

        $response->assertStatus(403);
    }

    public function test_search_page_forbidden_if_klink_not_configured_and_user_authenticated()
    {
        Option::option(Option::PUBLIC_CORE_ENABLED, false);
        config(['dms.are_guest_public_search_enabled' => true]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->actingAs($user)->get('/search');

        $response->assertStatus(403);
    }

    public function test_login_page_do_not_show_klink_search_if_enabled()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
        config(['dms.are_guest_public_search_enabled' => true]);
        
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('name="s"', false);
    }
}
