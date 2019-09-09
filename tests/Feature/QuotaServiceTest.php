<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Facades\UserQuota;
use KBox\Services\Quota;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\File;
use KBox\User;

class QuotaServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_null_default_configuration_represents_unlimited_storage()
    {
        config([
            'quota.user_storage_default' => null,
        ]);

        $this->assertEquals(INF, UserQuota::maximum());
        $this->assertTrue(UserQuota::isUnlimited());
    }

    public function test_default_configuration_is_retrieved()
    {
        config([
            'quota.user_storage_default' => 10,
        ]);

        $this->assertEquals(10, UserQuota::maximum());
    }

    public function test_quota_maximum_respect_user_preferences()
    {
        config([
            'quota.user_storage_default' => 10,
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $user->options()->create([
            'key' => 'quota',
            'value' => 100
        ]);

        $this->assertEquals(100, UserQuota::withUser($user)->maximum());
    }

    public function test_used_quota_calculation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $files = factory(File::class, 5)->create([
            'user_id' => $user->id,
            'size' => 4
        ]);

        $this->assertEquals(20, UserQuota::withUser($user)->used());
    }

    public function test_free_quota_with_unlimited_maximum()
    {
        config([
            'quota.user_storage_default' => null,
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $files = factory(File::class, 5)->create([
            'user_id' => $user->id,
            'size' => 4
        ]);

        $this->assertEquals(Quota::UNLIMITED, UserQuota::withUser($user)->free());
        $this->assertTrue(UserQuota::withUser($user)->accept(1000));
    }

    public function test_free_quota_with_zero_maximum()
    {
        config([
            'quota.user_storage_default' => 0,
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $files = factory(File::class, 5)->create([
            'user_id' => $user->id,
            'size' => 4
        ]);

        $this->assertEquals(0, UserQuota::withUser($user)->free());
        $this->assertFalse(UserQuota::withUser($user)->accept(1000));
    }

    public function test_free_quota_with_negative_maximum()
    {
        config([
            'quota.user_storage_default' => -10,
        ]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $files = factory(File::class, 5)->create([
            'user_id' => $user->id,
            'size' => 4
        ]);

        $this->assertEquals(0, UserQuota::withUser($user)->free());
        $this->assertFalse(UserQuota::withUser($user)->accept(1000));
    }
}
