<?php

namespace KBox\Support;

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
     * Retrieve the current support service settings
     *
     * @return array
     */
    public static function service()
    {
        $activeService = self::serviceName();

        return config('support.providers.'.$activeService) ?? [];
    }
    
    /**
     * Retrieve the current support service name
     *
     * @return string
     */
    public static function serviceName()
    {
        return Option::option(static::SUPPORT_SERVICE, null) ?? config('support.service');
    }

    /**
     * Check if support service is enabled
     *
     * @param string $service The service name to verify.
     * @return boolean
     */
    public static function active($service)
    {
        if ($service === 'uservoice') {
            return static::serviceName() === $service && static::token() !== null;
        }

        return static::serviceName() === $service;
    }
}
