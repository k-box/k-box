<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use Tests\Concerns\ClearDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\User;

class QuotaCheckCommandTest extends TestCase
{
    use ClearDatabase, DatabaseTransactions;

    public function test_quota_is_checked_for_all_users()
    {
        $users = factory(User::class, 10)->create();

        $this->artisan('quota:check')
            ->assertExitCode(0);

        $users->each(function ($user) {
            $this->assertNotNull($user->quota);
            $this->assertEquals(0, $user->quota->used);
        });
    }
    
    public function test_quota_is_checked_for_specified_users()
    {
        $users = factory(User::class, 5)->create();

        $arguments = $users->take(2)->mapWithKeys(function ($u) {
            return ['-u' => $u->id];
        })->toArray();

        $this->artisan('quota:check', $arguments)
            ->assertExitCode(0);

        $users->take(2)->each(function ($user) {
            $this->assertNotNull($user->quota);
            $this->assertEquals(0, $user->quota->used);
        });
    }
}
