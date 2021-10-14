<?php

namespace Tests\Unit;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\Events\EmailChanged;
use Illuminate\Support\Facades\Event;

class TrackEmailChangeActionTest extends TestCase
{
    public function test_email_changed_action_is_tracked_into_security_log()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        event(new EmailChanged($user, 'old@example.com', $user->email));

        $activities = $user->actions()->inLog('security')->where('description', 'email_changed')->get();

        $this->assertEquals(1, $activities->count(), "No email_changed activity logged");
        $this->assertEquals($user->id, $activities->first()->subject_id, "Activity subject is different than user");
        $this->assertEquals('old@example.com', $activities->first()->getExtraProperty('from'), "Activity from property is different");
        $this->assertEquals($user->email, $activities->first()->getExtraProperty('to'), "Activity to proeprty is different");
    }
}
