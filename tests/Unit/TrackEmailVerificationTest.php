<?php

namespace Tests\Unit;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TrackEmailVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_email_changed_action_is_tracked_into_security_log()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        event(new Verified($user));

        $activities = $user->actions()->inLog('security')->where('description', 'email_verified')->get();

        $this->assertEquals(1, $activities->count(), "No email_verified activity logged");
        $this->assertEquals($user->id, $activities->first()->subject_id, "Activity subject is different than user");
        $this->assertEquals($user->email, $activities->first()->getExtraProperty('email'), "Activity email property is different");
    }
}
