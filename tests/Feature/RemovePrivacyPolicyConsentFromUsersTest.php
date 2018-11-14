<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Consent;
use KBox\Consents;
use Tests\TestCase;
use KBox\Events\PrivacyPolicyUpdated;
use KBox\Listeners\RemovePrivacyPolicyConsentFromUsers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RemovePrivacyPolicyConsentFromUsersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_privacy_consent_is_removed_from_user()
    {
        $users = factory(User::class, 2)->create()->each(function ($u) {
            Consent::agree($u, Consents::PRIVACY);
        });

        (new RemovePrivacyPolicyConsentFromUsers())->handle(new PrivacyPolicyUpdated());

        $users->each(function ($u) {
            $this->assertFalse(Consent::isGiven(Consents::PRIVACY, $u), "Consent not withdrawn for user {$u->getKey()}");
        });
    }
}
