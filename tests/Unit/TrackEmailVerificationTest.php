<?php

namespace Tests\Unit;

use KBox\User;
use Tests\TestCase;
use Illuminate\Auth\Events\Verified;

class TrackEmailVerificationTest extends TestCase
{
    public function test_email_changed_action_is_tracked_into_security_log()
    {
        $user = tap(User::factory()->partner()->create(), function ($u) {
            $u->markEmailAsVerified();
        });

        event(new Verified($user));

        $activities = $user->actions()->inLog('security')->where('description', 'email_verified')->get();

        $this->assertEquals(1, $activities->count(), "No email_verified activity logged");
        $this->assertEquals($user->id, $activities->first()->subject_id, "Activity subject is different than user");
        $this->assertEquals($user->email, $activities->first()->getExtraProperty('email'), "Activity email property is different");
    }
}
