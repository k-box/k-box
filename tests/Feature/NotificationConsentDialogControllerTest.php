<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Consent;
use KBox\Flags;
use KBox\Consents;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Facades\Storage;

class NotificationConsentDialogControllerTest extends TestCase
{
    public function test_dialog_can_be_dismissed()
    {
        Storage::fake('app');

        Flags::enable(Flags::CONSENT_NOTIFICATIONS);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $response = $this->actingAs($user)->get(route('consent.dialog.notification.show'));

        $response->assertSuccessful();
        $response->assertSee(trans('consent.skip'));
        $response->assertSee(route('consent.dialog.statistic.show'));
    }
    
    public function test_dialog_not_presented_if_flag_is_disabled()
    {
        Storage::fake('app');

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $expected_url = route('consent.dialog.statistic.show');
        
        $response = $this->actingAs($user)->get(route('consent.dialog.notification.show'));
        
        $response->assertRedirect($expected_url);
    }

    public function test_dialog_can_be_accepted_and_redirects_to_statistic_consent()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $expected_url = route('consent.dialog.statistic.show');

        $response = $this->actingAs($user)->put(route('consent.dialog.notification.update'), [
            'notifications' => 1,
        ]);

        $response->assertRedirect($expected_url);

        $this->assertTrue(Consent::isGiven(Consents::NOTIFICATION, $user));
    }

    public function test_dialog_can_be_accepted_and_redirects_to_user_home()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);
        Consent::agree($user, Consents::STATISTIC);

        $expected_url = route('documents.index');

        $response = $this->actingAs($user)->put(route('consent.dialog.notification.update'), [
            'notifications' => 1,
        ]);

        $response->assertRedirect($expected_url);

        $this->assertTrue(Consent::isGiven(Consents::NOTIFICATION, $user));
    }
}
