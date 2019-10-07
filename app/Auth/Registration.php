<?php

namespace KBox\Auth;

/**
 * Registration.
 *
 * Enable access to Registration settings
 * and control the behavior of the registration
 */
class Registration
{
    
    /**
     * Check if users registration is enabled on this K-Box
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return filter_var(config('registration.enable'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * The registration requires an invitation
     *
     * @return bool
     */
    public static function requiresInvite()
    {
        return filter_var(config('registration.invite_required'), FILTER_VALIDATE_BOOLEAN);
    }
}
