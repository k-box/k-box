<?php

namespace Tests\Unit;

use KBox\User;
use Tests\TestCase;
use Illuminate\Auth\Events\PasswordReset;

class TrackPasswordResetActionTest extends TestCase
{
    public function test_password_reset_action_is_tracked_into_security_log()
    {
        $user = User::factory()->partner()->create();

        event(new PasswordReset($user));

        $activities = $user->actions()->inLog('security')->where('description', 'password_reset')->get();

        $this->assertEquals(1, $activities->count(), "No password_reset activity logged");
        $this->assertEquals($user->id, $activities->first()->subject_id, "Activity subject is different than user");
    }
}
