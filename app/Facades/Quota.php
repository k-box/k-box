<?php

namespace KBox\Facades;

use Illuminate\Support\Facades\Facade;
use KBox\Services\Quota as QuotaService;

/**
 * @see \KBox\Services\Quota
 */
class Quota extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return QuotaService::class;
    }
}
