<?php

namespace KBox;

use KBox\Pages\Page;

/**
 * Get the Home route for the specified user
 *
 * This is implemented as an invokable class so it can be mocked during tests, if needed
 */
class GetHomeRoute
{
    /**
     * Get the home page route for the specified user
     *
     * @param \KBox\User $user
     * @return string
     */
    public function __invoke(User $user)
    {
        $search = $user->can_capability(Capability::MAKE_SEARCH);
        $see_share = $user->can_capability(Capability::RECEIVE_AND_SEE_SHARE);
        $uploader = $user->can_all_capabilities([Capability::UPLOAD_DOCUMENTS, Capability::EDIT_DOCUMENT]);

        $privacyPageExists = optional(Page::find(Page::PRIVACY_POLICY_LEGAL))->isNotEmpty() ?? false;

        if (! Consent::isGiven(Consents::PRIVACY, $user) && $privacyPageExists) {
            // if user did not agree to the privacy policy
            // we must show the consent screen
            return route('consent.dialog.privacy.show');
        }
    
        if ($uploader) {
            if (flags(Flags::HOME_ROUTE_PROJECTS)) {
                return route('documents.projects.index');
            }

            if (flags(Flags::HOME_ROUTE_RECENT)) {
                return route('documents.recent');
            }

            return route('documents.index');
        } elseif (! $search && $see_share) {
            return route('shares.index');
        } else {
            return route('search');
        }
    }
}
