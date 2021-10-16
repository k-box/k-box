<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Flags;
use KBox\Consent;
use KBox\Consents;
use Tests\TestCase;
use Jenssegers\Date\Date as LocalizedDate;

class UserPrivacyControllerTest extends TestCase
{
    public function test_privacy_page_shows_consent_status()
    {
        $user = User::factory()->admin()->create();

        Flags::enable(Flags::CONSENT_NOTIFICATIONS);

        Consent::agree($user, Consents::PRIVACY);
        Consent::agree($user, Consents::NOTIFICATION);
        Consent::withdraw($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->get(route('profile.privacy.index'));
            
        $response->assertSuccessful();
        $response->assertViewIs('profile.privacy');

        $response->assertViewHas('consent_privacy_given', true);
        $response->assertViewHas('consent_notification_given', true);
        $response->assertViewHas('consent_statistics_given', false);

        $expected_date = LocalizedDate::instance(now())->format(trans('units.date_format'));

        $response->assertViewHas('consent_privacy_activity', trans('profile.privacy.activity.consent_given', ['date' => $expected_date]));
        $response->assertViewHas('consent_notification_activity', trans('profile.privacy.activity.consent_given', ['date' => $expected_date]));

        $response->assertSee('notifications" value="0', false);
        $response->assertSee('statistics" value="1', false);
    }

    public function test_notification_consent_can_be_given()
    {
        $user = User::factory()->admin()->create();

        Flags::enable(Flags::CONSENT_NOTIFICATIONS);

        Consent::withdraw($user, Consents::NOTIFICATION);

        $response = $this->actingAs($user)->put(route('profile.privacy.update'), [
            'notifications' => '1'
        ]);

        $this->assertTrue(Consent::isGiven(Consents::NOTIFICATION, $user));

        $response->assertRedirect(route('profile.privacy.index'));

        $response = $this->actingAs($user)->get(route('profile.privacy.index'));

        $response->assertViewHas('consent_notification_given', true);
        $response->assertSee('notifications" value="0', false);
    }
    
    public function test_notification_consent_can_be_removed()
    {
        $user = User::factory()->admin()->create();

        Flags::enable(Flags::CONSENT_NOTIFICATIONS);

        Consent::agree($user, Consents::NOTIFICATION);

        $response = $this->actingAs($user)->put(route('profile.privacy.update'), [
            'notifications' => 0
        ]);

        $this->assertFalse(Consent::isGiven(Consents::NOTIFICATION, $user));

        $response->assertRedirect(route('profile.privacy.index'));

        $response = $this->actingAs($user)->get(route('profile.privacy.index'));

        $response->assertViewHas('consent_notification_given', false);
        $response->assertSee('notifications" value="1', false);
    }

    public function test_statistic_consent_can_be_given()
    {
        $user = User::factory()->admin()->create();

        Flags::enable(Flags::CONSENT_NOTIFICATIONS);

        Consent::withdraw($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->put(route('profile.privacy.update'), [
            'statistics' => 1
        ]);

        $this->assertTrue(Consent::isGiven(Consents::STATISTIC, $user));

        $response->assertRedirect(route('profile.privacy.index'));

        $response = $this->actingAs($user)->get(route('profile.privacy.index'));

        $response->assertViewHas('consent_statistics_given', true);
        $response->assertSee('statistics" value="0', false);
    }
    
    public function test_statistic_consent_can_be_removed()
    {
        $user = User::factory()->admin()->create();

        Consent::agree($user, Consents::STATISTIC);

        $response = $this->actingAs($user)->put(route('profile.privacy.update'), [
            'statistics' => 0
        ]);

        $this->assertFalse(Consent::isGiven(Consents::STATISTIC, $user));

        $response->assertRedirect(route('profile.privacy.index'));

        $response = $this->actingAs($user)->get(route('profile.privacy.index'));

        $response->assertViewHas('consent_statistics_given', false);
        $response->assertSee('statistics" value="1', false);
    }
}
