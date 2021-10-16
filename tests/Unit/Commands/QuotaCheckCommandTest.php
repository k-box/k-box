<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use KBox\User;

class QuotaCheckCommandTest extends TestCase
{
    public function test_quota_is_checked_for_all_users()
    {
        $users = User::factory()->count(2)->create();

        $this->artisan('quota:check')
            ->assertExitCode(0);

        $users->each(function ($user) {
            $this->assertNotNull($user->quota);
            $this->assertEquals(0, $user->quota->used);
        });
    }
    
    public function test_quota_is_checked_for_specified_users()
    {
        $users = User::factory()->count(3)->create();

        $interestingUsers = $users->take(2);
        $skippedUsers = $users->skip(2)->take(1);

        $arguments = ['-u' => $interestingUsers->pluck('id')];

        $this->artisan('quota:check', $arguments)
            ->assertExitCode(0);

        $interestingUsers->each(function ($user) {
            $this->assertNotNull($user->quota);
            $this->assertEquals(0, $user->quota->used);
        });
        $skippedUsers->each(function ($user) {
            $this->assertNull($user->quota);
        });
    }
}
