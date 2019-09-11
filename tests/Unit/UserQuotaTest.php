<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use KBox\Notifications\QuotaFullNotification;
use KBox\Notifications\QuotaLimitNotification;
use KBox\Quota;
use KBox\UserQuota;

class UserQuotaTest extends TestCase
{
    use DatabaseTransactions;

    public function quota_value_provider()
    {
        return [
            // configuration, value override, unlimited override, limit value expected, unlimited attribute value expected
            [null, null, false, INF, true ],
            [-10 , null, false, 0  , false],
            [0   , null, false, 0  , false],
            [10  , null, false, 10 , false],
            [null, 10  , false, 10 , false],
            [10  , -10 , false, 0  , false],
            [10  , -10 , false, 0  , false],
            [10  , 100 , true , null, true ],
        ];
    }

    public function threshold_value_provider()
    {
        return [
            [null, null, Quota::DEFAULT_THRESHOLD],
            [-10, null, 0],
            [0, null, 0],
            [10, null, 10],
            [10, 80, 80],

        ];
    }

    public function test_notified_attribute()
    {
        $quota = (new UserQuota());

        $this->assertFalse($quota->notified);
        
        $quota->notified = true;

        $this->assertTrue($quota->notified);
        $this->assertNotNull($quota->notification_sent_at);
        $this->assertInstanceOf(Carbon::class, $quota->notification_sent_at);
    }
    
    public function test_notified_full_attribute()
    {
        $quota = (new UserQuota());

        $this->assertFalse($quota->notified_full);
        
        $quota->notified_full = true;

        $this->assertTrue($quota->notified_full);
        $this->assertNotNull($quota->full_notification_sent_at);
        $this->assertInstanceOf(Carbon::class, $quota->full_notification_sent_at);
    }
    
    /**
     * @dataProvider quota_value_provider
     */
    public function test_limit_attribute($configuration, $value, $unlimited_value, $expected_limit, $expected_unlimited)
    {
        config([
            'quota.user' => $configuration,
        ]);

        $quota = (new UserQuota())->forceFill([
            'limit' => $value,
            'unlimited' => $unlimited_value
        ]);

        $this->assertEquals($expected_limit, $quota->limit, 'Limit value');
        $this->assertEquals($expected_unlimited, $quota->unlimited, 'Unlimited value');
    }
    
    /**
     * @dataProvider threshold_value_provider
     */
    public function test_threshold_attribute($configuration, $value, $expected)
    {
        config([
            'quota.threshold' => $configuration,
        ]);

        $quota = (new UserQuota())->forceFill([
            'threshold' => $value
        ]);

        $this->assertEquals($expected, $quota->threshold);
    }

    public function test_is_above_threshold_attribute()
    {
        $quota = (new UserQuota())->forceFill([
            'limit' => 100,
            'threshold' => 50,
            'used' => 50,
        ]);

        $quota2 = (new UserQuota())->forceFill([
            'limit' => 100,
            'threshold' => 50,
            'used' => 40,
        ]);

        $this->assertTrue($quota->is_above_threshold);

        $this->assertFalse($quota2->is_above_threshold);
    }

    public function test_is_full_attribute()
    {
        $quota = (new UserQuota())->forceFill([
            'limit' => 100,
            'threshold' => 50,
            'used' => 99,
        ]);

        $quota2 = (new UserQuota())->forceFill([
            'limit' => 100,
            'threshold' => 50,
            'used' => 40,
        ]);

        $this->assertTrue($quota->is_full);
        $this->assertFalse($quota2->is_full);
    }

    public function test_no_notification_sent()
    {
        Notification::fake();

        $quota = factory(UserQuota::class)->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 1,
        ]);

        $quota->notify();

        Notification::assertNothingSent();
    }

    public function test_above_threshold_notification_sent()
    {
        Notification::fake();

        $quota = factory(UserQuota::class)->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 52,
        ]);

        $quota->notify();

        $this->assertTrue($quota->notified);

        Notification::assertSentTo(
            $quota->user,
            QuotaLimitNotification::class,
            function ($notification, $channels) use ($quota) {
                return $notification->quota->is($quota);
            }
        );
    }

    public function test_full_notification_sent()
    {
        Notification::fake();

        $quota = factory(UserQuota::class)->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 99,
        ]);

        $quota->notify();

        $this->assertTrue($quota->notified_full);

        Notification::assertSentTo(
            $quota->user,
            QuotaFullNotification::class,
            function ($notification, $channels) use ($quota) {
                return $notification->quota->is($quota);
            }
        );
    }

    public function test_reducing_storage_pressure_clears_notifications()
    {
        Notification::fake();

        $quota = factory(UserQuota::class)->create([
            'limit' => 100,
            'threshold' => 50,
            'used' => 99,
        ]);

        $quota->notify();

        $quota->calculate();

        $this->assertFalse($quota->notified_full);
        $this->assertFalse($quota->notified);
    }
}
