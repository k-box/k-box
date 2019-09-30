<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use KBox\Invite;
use KBox\Jobs\PurgeExpiredInvites;

class PurgeExpiredInvitesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_job_purges_expired_invites()
    {
        $expired_invites = factory(Invite::class, 3)->create([
            'expire_at' => now()->subDays(config('invite.expiration') + 1)
        ]);

        $job = new PurgeExpiredInvites;
        $job->handle();
        
        $expired_invites->each(function ($invite) {
            $this->assertNull($invite->fresh());
        });
    }
    
    public function test_command_purges_expired_invites()
    {
        $expired_invites = factory(Invite::class, 3)->create([
            'expire_at' => now()->subDays(config('invite.expiration') + 1)
        ]);

        $this->artisan('invite:purge')
            ->expectsOutput('Purging expired invites...')
            ->expectsOutput('completed')
            ->assertExitCode(0);
        
        $expired_invites->each(function ($invite) {
            $this->assertNull($invite->fresh());
        });
    }
}
