<?php

namespace Tests\Feature;

use Tests\TestCase;

use Illuminate\Support\Facades\View;
use KBox\Capability;
use KBox\User;
use KBox\UserQuota;

class QuotaWidgetTest extends TestCase
{
    public function test_quota_widget_is_defined()
    {
        $this->assertTrue(View::exists('quota.widget'));
    }

    public function test_quota_widget_is_present_on_the_page()
    {
        $user = tap(User::factory()->create())->addCapabilities(Capability::$PARTNER);

        $quota = factory(UserQuota::class)->create([
            'user_id' => $user->id,
            'limit' => 2048,
            'threshold' => 50,
            'used' => 1024,
            'unlimited' => false,
        ]);

        $response = $this->actingAs($user)->get('/documents');

        $response->assertStatus(200);

        $response->assertSee(trans('widgets.storage.title'));
        $response->assertSee(trans('widgets.storage.used', ['used' => '1.00 KB', 'total' => '2.00 KB']));
    }

    public function test_quota_widget_not_show_if_unlimited()
    {
        $user = tap(User::factory()->create())->addCapabilities(Capability::$PARTNER);

        $quota = factory(UserQuota::class)->create([
            'user_id' => $user->id,
            'limit' => 2048,
            'threshold' => 50,
            'used' => 1024,
            'unlimited' => true,
        ]);

        $response = $this->actingAs($user)->get('/documents');

        $response->assertStatus(200);

        $response->assertDontSee(trans('widgets.storage.title'));
        $response->assertDontSee(trans('widgets.storage.used', ['used' => '1.00 KB', 'total' => '2.00 KB']));
    }

    public function test_quota_widget_composer()
    {
        $user = tap(User::factory()->create())->addCapabilities(Capability::$PARTNER);

        $quota = factory(UserQuota::class)->create([
            'user_id' => $user->id,
            'limit' => 2048,
            'threshold' => 50,
            'used' => 1024,
            'unlimited' => false,
        ]);

        $this->actingAs($user);

        $view = $this->view('quota.widget');

        $this->assertEquals([
            'unlimited' => false,
            'percentage' => 50,
            'used' => '1.00 KB',
            'total' => '2.00 KB',
        ], $view->getData());
    }
    
    public function test_quota_widget_composer_skip_if_not_authenticated()
    {
        $user = tap(User::factory()->create())->addCapabilities(Capability::$PARTNER);

        $quota = factory(UserQuota::class)->create([
            'user_id' => $user->id,
            'limit' => 2048,
            'threshold' => 50,
            'used' => 1024,
            'unlimited' => false,
        ]);

        $view = $this->view('quota.widget');

        $this->assertEquals([], $view->getData());
    }

    /**
     * Load a view and attempt to call all registered view composers
     *
     * @param string $view the name of the view
     * @return \Illuminate\View\View
     */
    protected function view($view, array $data = [])
    {
        return tap(View::make($view), function ($v) {
            View::callComposer($v);
        });
    }
}
