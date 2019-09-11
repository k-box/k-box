<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Facades\UserQuota;
use KBox\Quota;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\File;
use KBox\User;

class QuotaTest extends TestCase
{
    use DatabaseTransactions;

    public function quota_value_provider()
    {
        return [
            [null, INF],
            [-10, 0],
            [0, 0],
            [10, 10],
            [false, 0],
            [true, 0],
            [[], 0],
            ["a string", 0],
            ["100", 100],
            ["-100", 0],
            ["null", 0],
        ];
    }

    public function threshold_value_provider()
    {
        return [
            [null, Quota::DEFAULT_THRESHOLD],
            [-10, 0],
            [0, 0],
            [10, 10],
            [false, Quota::DEFAULT_THRESHOLD],
            [true, Quota::DEFAULT_THRESHOLD],
            [[], Quota::DEFAULT_THRESHOLD],
            ["a string", 0],
            ["100", 100],
            ["-100", 0],
            ["null", 0],
        ];
    }

    /**
     * @dataProvider quota_value_provider
     */
    public function test_limit_configuration($value, $expected)
    {
        config([
            'quota.user' => $value,
        ]);

        $this->assertEquals($expected, Quota::limit());
    }

    public function test_is_unlimited()
    {
        config([
            'quota.user' => null,
        ]);

        $this->assertTrue(Quota::isUnlimited());
    }

    public function test_not_unlimited()
    {
        config([
            'quota.user' => 10,
        ]);

        $this->assertFalse(Quota::isUnlimited());
    }
    
    /**
     * @dataProvider threshold_value_provider
     */
    public function test_threshold_is_returned($value, $expected)
    {
        config([
            'quota.threshold' => $value,
        ]);

        $this->assertEquals($expected, Quota::threshold());
    }

    // public function test_quota_maximum_respect_user_preferences()
    // {
    //     config([
    //         'quota.user' => 10,
    //     ]);

    //     $user = tap(factory(User::class)->create(), function ($u) {
    //         $u->addCapabilities(Capability::$PARTNER);
    //     });

    //     $user->options()->create([
    //         'key' => 'quota',
    //         'value' => 100
    //     ]);

    //     $this->assertEquals(100, UserQuota::withUser($user)->maximum());
    // }

    // public function test_used_quota_calculation()
    // {
    //     $user = tap(factory(User::class)->create(), function ($u) {
    //         $u->addCapabilities(Capability::$PARTNER);
    //     });

    //     $files = factory(File::class, 5)->create([
    //         'user_id' => $user->id,
    //         'size' => 4
    //     ]);

    //     $this->assertEquals(20, UserQuota::withUser($user)->used());
    // }

    // public function test_free_quota_with_unlimited_maximum()
    // {
    //     config([
    //         'quota.user' => null,
    //     ]);

    //     $user = tap(factory(User::class)->create(), function ($u) {
    //         $u->addCapabilities(Capability::$PARTNER);
    //     });

    //     $files = factory(File::class, 5)->create([
    //         'user_id' => $user->id,
    //         'size' => 4
    //     ]);

    //     $this->assertEquals(Quota::UNLIMITED, UserQuota::withUser($user)->free());
    //     $this->assertTrue(UserQuota::withUser($user)->accept(1000));
    // }

    // public function test_free_quota_with_zero_maximum()
    // {
    //     config([
    //         'quota.user' => 0,
    //     ]);

    //     $user = tap(factory(User::class)->create(), function ($u) {
    //         $u->addCapabilities(Capability::$PARTNER);
    //     });

    //     $files = factory(File::class, 5)->create([
    //         'user_id' => $user->id,
    //         'size' => 4
    //     ]);

    //     $this->assertEquals(0, UserQuota::withUser($user)->free());
    //     $this->assertFalse(UserQuota::withUser($user)->accept(1000));
    // }

    // public function test_free_quota_with_negative_maximum()
    // {
    //     config([
    //         'quota.user' => -10,
    //     ]);

    //     $user = tap(factory(User::class)->create(), function ($u) {
    //         $u->addCapabilities(Capability::$PARTNER);
    //     });

    //     $files = factory(File::class, 5)->create([
    //         'user_id' => $user->id,
    //         'size' => 4
    //     ]);

    //     $this->assertEquals(0, UserQuota::withUser($user)->free());
    //     $this->assertFalse(UserQuota::withUser($user)->accept(1000));
    // }
}
