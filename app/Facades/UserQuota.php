<?php

namespace KBox\Facades;

use KBox\Services\Quota;
use Illuminate\Support\Facades\Facade;

/**
 * @see \KBox\Services\Quota
 */
class UserQuota extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Quota::class;
    }
}
