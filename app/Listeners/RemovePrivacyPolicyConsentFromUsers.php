<?php

namespace KBox\Listeners;

use Log;
use KBox\Consent;
use KBox\Consents;
use KBox\Events\PrivacyPolicyUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Removes the consent that was expressed by users about the privacy policy.
 *
 * This is performed as handling of the PrivacyPolicyUpdated event
 */
class RemovePrivacyPolicyConsentFromUsers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PrivacyPolicyUpdated $event)
    {
        Log::info("Cleaning Privacy consents");
        Consent::where('consent_topic', Consents::PRIVACY)->delete();
    }
}
