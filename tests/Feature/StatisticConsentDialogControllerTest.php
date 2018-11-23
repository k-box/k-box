<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Consent;
use KBox\Consents;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StatisticConsentDialogControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dialog_can_be_dismissed()
    {
        Storage::fake('app');

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $response = $this->actingAs($user)->get(route('consent.dialog.statistic.show'));

        $response->assertSuccessful();
        $response->assertSee(trans('consent.skip'));
    }

    public function test_dialog_can_be_fully_accepted()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $expected_url = route('documents.index');

        $response = $this->actingAs($user)->put(route('consent.dialog.statistic.update'), [
            'statistics' => '1',
        ]);

        $response->assertRedirect($expected_url);

        $this->assertTrue(Consent::isGiven(Consents::STATISTIC, $user));
    }
}
