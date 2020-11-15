<?php

namespace KBox\Facades;

use Oneofftech\Identities\Facades\Identity as Facade;

/**
 * @see \Oneofftech\Identities\Facades\Identity
 * @see \Oneofftech\Identities\IdentitiesManager
 */
class Identity extends Facade
{
    
    /**
     * If the oauth identity feature is enabled
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return ! empty(self::enabledProviders());
    }

    /**
     * The enabled identity providers
     *
     * @return array
     */
    public static function enabledProviders()
    {
        $config = config('identities.providers') ?? null;

        if (is_null($config)) {
            return [];
        }

        $providers = explode(',', $config);

        return $providers;
    }
}
