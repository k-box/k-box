<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Consent;
use KBox\Consents;
use Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class ConsentTest extends TestCase
{
    public function test_consent_agree_activity_is_logged()
    {
        $user = User::factory()->partner()->create();

        Consent::agree($user, Consents::PRIVACY);

        $this->assertTrue(Consent::isGiven(Consents::PRIVACY, $user), "Expected consent not given");

        $activity = Activity::all()->last();

        $this->assertNotNull($activity, "activity not stored after givin consent");
        $this->assertEquals('consent', $activity->log_name);
        $this->assertEquals('created', $activity->description);
        $this->assertInstanceOf(Consent::class, $activity->subject);
        $this->assertNull($activity->causer);
        $this->assertEquals($user->getKey(), $activity->subject->user_id);
        $this->assertEquals(Consents::PRIVACY, $activity->subject->consent_topic);
    }
    
    public function test_consent_withdrawal_activity_is_logged()
    {
        $user = User::factory()->partner()->create();
        Consent::agree($user, Consents::PRIVACY);

        Consent::withdraw($user, Consents::PRIVACY);

        $this->assertFalse(Consent::isGiven(Consents::PRIVACY, $user), "Expected consent was not removed from database");

        $activity = Activity::all()->last();
        
        $this->assertNotNull($activity, "activity not stored after givin consent");
        $this->assertEquals('consent', $activity->log_name);
        $this->assertEquals('deleted', $activity->description);
        $this->assertEquals([
            'attributes' => [
                'user_id' => $user->getKey(),
                'consent_topic' => Consents::PRIVACY
            ]
        ], $activity->changes->toArray());
        $this->assertNull($activity->subject);
        $this->assertNull($activity->causer);
    }
}
