<?php

namespace KBox\Support;

use KBox\User;
use KBox\Option;

class SupportService
{
    /**
     * The option that stores the key for the UserVoice support service
     */
    const SUPPORT_TOKEN = 'support_token';
    
    /**
     * The option that stores the current analytics service
     */
    const SUPPORT_SERVICE = 'support_service';
    
    /**
     * Get the support service access token.
     *
     * First the stored option is checked, then the static deploy configuration
     *
     * @return string|null the support ticket integration token if configured, null otherwise
     */
    public static function token()
    {
        $conf = static::service();

        return Option::option(static::SUPPORT_TOKEN, null) ?? $conf['token'] ?? null;
    }
    
    /**
     * Retrieve the current analytics service settings
     *
     * @return array
     */
    protected static function service()
    {
        $activeService = self::serviceName();

        return config('support.providers.'.$activeService) ?? [];
    }
    
    /**
     * Retrieve the current analytics service settings
     *
     * @return array
     */
    public static function serviceName()
    {
        return Option::option(static::SUPPORT_SERVICE, null) ?? config('support.service');
    }

    /**
     * Check if support service is enabled
     *
     * @param string $service The service to verify. Default uservoice
     * @return boolean
     */
    public static function active($service = 'uservoice')
    {
        return static::serviceName() === $service && static::token() !== null;
    }

}
