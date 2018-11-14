<?php

namespace Tests\Feature;

use Auth;
use KBox\User;
use KBox\Consent;
use KBox\Consents;
use Tests\TestCase;
use KBox\Capability;
use KBox\Pages\Page;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivacyConsentDialogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_consent_dialog_is_presented_if_user_did_not_accept_privacy_policy()
    {
        Storage::fake('app');

        $user = tap(factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('jane-super-secret')
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $page = tap(Page::create([
            'id' => Page::PRIVACY_POLICY_LEGAL,
            'title' => 'Example legal privacy policy',
            'description' => 'A descriptive text',
            'authors' => 1,
            'content' => '## page content'
        ]), function ($page) {
            $page->save();
        });

        $expected_url = '/consent/privacy';

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'jane-super-secret'
        ]);

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
        
        $response->assertRedirect($expected_url);
    }

    public function test_consent_dialog_not_presented_if_user_agreed_to_privacy_policy()
    {
        $user = tap(factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('jane-super-secret')
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $expected_url = route('documents.index');

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'jane-super-secret'
        ]);

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
        
        $response->assertRedirect($expected_url);
    }

    public function test_consent_dialog_visible_only_by_authenticated_users()
    {
        $url = route('consent.dialog.privacy.show');
        $expected_url = '/';

        $response = $this->get($url);
        
        $response->assertRedirect($expected_url);
    }

    public function test_consent_dialog_redirects_if_user_gave_consent()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        Consent::agree($user, Consents::PRIVACY);

        $url = route('consent.dialog.privacy.show');
        $expected_url = route('documents.index');

        $response = $this->actingAs($user)->get($url);

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
        
        $response->assertRedirect($expected_url);
    }

    public function test_privacy_be_agreed_from_consent_dialog()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $url = route('consent.dialog.privacy.update');
        $expected_url = route('documents.index');

        $response = $this->actingAs($user)->put($url, [
            'agree' => 'privacy',
        ]);

        $response->assertRedirect($expected_url);
        $this->assertTrue(Consent::isGiven(Consents::PRIVACY, $user), "Expected consent not given");

        $activity = Activity::all()->last();

        $this->assertNotNull($activity, "activity not stored after givin consent");
        $this->assertEquals('consent', $activity->log_name);
        $this->assertEquals('created', $activity->description);
        $this->assertInstanceOf(Consent::class, $activity->subject);
        $this->assertInstanceOf(User::class, $activity->causer);
        $this->assertEquals($user->getKey(), $activity->causer->getKey());
        $this->assertEquals($user->getKey(), $activity->subject->user_id);
        $this->assertEquals(Consents::PRIVACY, $activity->subject->consent_topic);
    }
}
