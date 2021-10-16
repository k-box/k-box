<?php

namespace Tests\Feature;

use KBox\UserQuota;
use Tests\TestCase;

use Illuminate\Support\Facades\Queue;
use KBox\Jobs\CalculateUserUsedQuota;

class ProfileQuotaControllerTest extends TestCase
{
    public function test_storage_profile_shows_current_status_when_user_quota_present()
    {
        $quota = UserQuota::factory()->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 1,
            'unlimited' => false,
        ]);

        $response = $this->actingAs($quota->user)->get(route('profile.storage.index'));

        $response->assertStatus(200);

        $response->assertViewHasAll([

            'threshold' => $quota->threshold,

            'unlimited' => $quota->unlimited,
            
            'percentage' => $quota->used_percentage,
            
            'is_above_threshold' => $quota->is_above_threshold,
            'is_full' => $quota->is_full,

            'notification_sent_at',
            'full_notification_sent_at',

            'used' => human_filesize($quota->used),
            'total' => human_filesize($quota->limit),
            
            'breadcrumb_current' => trans('profile.storage.title'),
            'pagetitle' => trans('profile.storage.title'),
        ]);

        $response->assertSee(trans('profile.storage.view_trash'), false);
        $response->assertSee(trans('quota.threshold.section'), false);
    }
    
    public function test_storage_profile_shows_unlimited_info()
    {
        $quota = UserQuota::factory()->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 1,
            'unlimited' => true,
        ]);

        $response = $this->actingAs($quota->user)->get(route('profile.storage.index'));

        $response->assertStatus(200);

        $response->assertViewHasAll([

            'threshold' => $quota->threshold,

            'unlimited' => $quota->unlimited,
            
            'percentage' => $quota->used_percentage,
            
            'is_above_threshold' => $quota->is_above_threshold,
            'is_full' => $quota->is_full,

            'notification_sent_at',
            'full_notification_sent_at',

            'used' => human_filesize($quota->used),
            'total' => human_filesize($quota->limit),

            'breadcrumb_current' => trans('profile.storage.title'),
            'pagetitle' => trans('profile.storage.title'),
        ]);

        $response->assertSee(trans('quota.unlimited_label'));
        $response->assertDontSee(trans('profile.storage.view_trash'));
        $response->assertDontSee(trans('quota.threshold.section'));
    }
    
    public function test_threshold_change_is_denied_for_unlimited()
    {
        $quota = UserQuota::factory()->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 1,
            'unlimited' => true,
        ]);

        $response = $this->actingAs($quota->user)
            ->from(route('profile.storage.index'))
            ->put(route('profile.storage.update'), [
                'threshold' => 60,
            ]);

        $response->assertRedirect(route('profile.storage.index'));
        $response->assertSessionHasErrors([
            'threshold'
        ]);
    }

    public function invalid_threshold_value_provider()
    {
        return [
            ['string'],
            [false],
            [true],
            [null],
            [''],
            [[]],
            [-10],
            [0], // minimum is 5
            [4], // minimum is 5
            [120],
            [99], // maximum is 98, as the 99+ is covered by the full notification
            [100], // maximum is 98, as the 99+ is covered by the full notification
        ];
    }
    
    /**
     * @dataProvider invalid_threshold_value_provider
     */
    public function test_threshold_change_is_denied_with_unacceptable_value($value)
    {
        $quota = UserQuota::factory()->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 1,
            'unlimited' => false,
        ]);

        $response = $this->actingAs($quota->user)
            ->from(route('profile.storage.index'))
            ->put(route('profile.storage.update'), [
                'threshold' => $value,
            ]);

        $response->assertRedirect(route('profile.storage.index'));
        $response->assertSessionHasErrors([
            'threshold'
        ]);
    }
    
    public function test_threshold_change()
    {
        Queue::fake();

        $quota = UserQuota::factory()->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 1,
            'unlimited' => false,
        ]);

        $response = $this->actingAs($quota->user)
            ->from(route('profile.storage.index'))
            ->put(route('profile.storage.update'), [
                'threshold' => 60,
            ]);

        $response->assertRedirect(route('profile.storage.index'));
        $response->assertSessionHas('flash_message', trans('quota.threshold.updated', ['threshold' => 60]));

        $this->assertEquals($quota->fresh()->threshold, 60);

        Queue::assertPushed(CalculateUserUsedQuota::class);
    }
}
