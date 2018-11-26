<?php

namespace KBox;

use KBox\Traits\HasEnums;

/**
 * Consent topics
 *
 * The macro category of consent that the K-Box can ask to users.
 *
 * Each topic can have sub-consents or specific configurations
 */
class Consents
{
    use HasEnums;

    /**
     * Privacy policy consent.
     *
     * The user agreed to the privacy policy
     */
    const PRIVACY = 1;

    /**
     * Notifications consent.
     *
     * The user agreed to receive notifications
     */
    const NOTIFICATION = 2;

    /**
     * Statistics Consent.
     *
     * The user has agreed to be profiled during page browsing
     */
    const STATISTIC = 3;
}
