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
        return Option::option(static::ANALYTICS_TOKEN, null) ?? config('analytics.token');
    }
    
    /**
     * Retrieve the current analytics service settings
     * 
     * @return array
     */
    protected static function service()
    {
        $activeService = self::serviceName();

        return config('analytics.services.' . $activeService) ?? [];
    }
    
    /**
     * Retrieve the current analytics service settings
     * 
     * @return array
     */
    public static function serviceName()
    {
        return Option::option(static::ANALYTICS_SERVICE, null) ?? config('analytics.service');
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
    public static function configuration($option = null, $default = null)
    {
        $config = collect(static::service())->except('view');

        $config->put('token', static::token());

        $additional_options = json_decode(Option::option(static::ANALYTICS_CONFIGURATION, '{}'), true);
        if($additional_options && is_array($additional_options)){

            $config = $config->merge($additional_options);
        }

        if(is_null($option)){

            return $config->toArray();
        }

        return $config->get($option, $default);
    }
    
}
