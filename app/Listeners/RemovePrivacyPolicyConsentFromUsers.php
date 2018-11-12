<?php

namespace KBox\Listeners;

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
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PrivacyPolicyUpdated $event)
    {
        //
    }
}
