<?php

namespace KBox\Support\Analytics;

use KBox\User;
use KBox\Option;
use KBox\Consent;
use KBox\Consents;
use Illuminate\Database\Eloquent\Model;


class Analytics
{
    /**
     * The option that stores the Analytics token
     */
    const ANALYTICS_TOKEN = 'analytics_token';
    
    /**
     * The option that stores the custom configuration
     */
    const ANALYTICS_CONFIGURATION = 'analytics_configuration';
    
    /**
     * The option that stores the current analytics service
     */
    const ANALYTICS_SERVICE = 'analytics_service';
    
    /**
     * Get the analytics tracking token.
     *
     * @return string|boolean the anlytics site id to be used in the Piwik analytics code
     */
    public static function token()
    {
        return config('analytics.token') ?? Option::option(static::ANALYTICS_TOKEN, null);
    }
    
    /**
     * Retrieve the current analytics service settings
     * 
     * @return array
     */
    protected static function service()
    {
        $activeService = config('analytics.service') ?? Option::option(static::ANALYTICS_SERVICE, null);

        return config('analytics.services.' . $activeService) ?? [];
    }


    /**
     * Check if analytics is enabled for a user
     * 
     * @param User $user The user to check for analytics collection consent. Default null, currently logged-in user
     * @return boolean
     */
    public static function isActive(?User $user = null)
    {
        return static::token() !== null && Consent::isGiven(Consents::STATISTIC, $user);
    }

    /**
     * The anaytics view to include
     * 
     * @return string
     */
    public static function view()
    {
        $config = static::service();

        return $config['view'] ?? 'analytics.none';
    }

    /**
     * The analytics configuration to pass to the view
     * 
     * @return array
     */
    public static function configuration()
    {
        $config = collect(static::service());

        $config->put('token', static::token());

        return $config->except('view')->toArray();
    }
    
}
