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
        $partner = $user->can_all_capabilities(Capability::$PARTNER);

        if (! Consent::isGiven(Consents::PRIVACY, $user) && ! Page::find(Page::PRIVACY_POLICY_LEGAL)->isEmpty()) {
            // if user did not agree to the privacy policy
            // we must show the consent screen
            return route('consent.dialog.privacy.show');
        }
    
        if ($user->isDMSManager()) {
            return route('documents.index');
        } elseif ($user->isContentManager() ||
                $user->can_capability(Capability::UPLOAD_DOCUMENTS) ||
                $user->can_capability(Capability::EDIT_DOCUMENT)) {
            //documents manager redirect to documents
            return route('documents.index');
        } elseif ($search && ! $see_share) {
            return route('search');
        } elseif (! $search && $see_share) {
            return route('shares.index');
        } else {
            //poor child redirect to search
            return route('search');
        }
    }
}
