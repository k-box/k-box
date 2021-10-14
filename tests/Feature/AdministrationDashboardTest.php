<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;

class AdministrationDashboardTest extends TestCase
{
    public function test_administrator_sees_admin_link_in_header()
    {
        $user = factory(User::class)->state('admin')->create();

        $response = $this
            ->actingAs($user)
            ->get(route('administration.index'));

        $response->assertStatus(200);

        $response->assertSee(trans('administration.page_title'));
    }

    public function test_administrator_sees_dashboard()
    {
        $user = factory(User::class)->state('admin')->create();

        $response = $this
            ->actingAs($user)
            ->get(route('administration.index'));

        $response->assertStatus(200);
    }

    public function test_administrator_dashboard_require_authentication()
    {
        $response = $this->get(route('administration.index'));

        $response->assertRedirect(url('/'));
    }

    public function test_access_denied_for_non_administrators()
    {
        $user = factory(User::class)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('administration.index'));

        $response->assertForbidden();
    }
}
