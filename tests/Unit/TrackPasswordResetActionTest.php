<?php

namespace Tests\Unit;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TrackPasswordResetActionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_password_reset_action_is_tracked_into_security_log()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        event(new PasswordReset($user));

        $activities = $user->actions()->inLog('security')->where('description', 'password_reset')->get();

        $this->assertEquals(1, $activities->count(), "No password_reset activity logged");
        $this->assertEquals($user->id, $activities->first()->subject_id, "Activity subject is different than user");
    }
}
