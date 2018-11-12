<?php

namespace KBox\Listeners;

use KBox\Pages\Page;
use KBox\Events\PageChanged;
use KBox\Events\PrivacyPolicyUpdated;

/**
 * Transform a PageChanged event into a PrivacyPolicyUpdated event.
 *
 * This listener convert a PageChanged event to a PrivacyPolicyUpdated
 * event in case the page being changed is the privacy policy page
 */
class TransformPageToPolicyEvent
{
    /**
     * Handle the event.
     *
     * @param  PageChanged  $event
     * @return void
     */
    public function handle(PageChanged $event)
    {
        if ($event->page->id === Page::PRIVACY_POLICY_LEGAL) {
            event(new PrivacyPolicyUpdated());
        }
    }
}
