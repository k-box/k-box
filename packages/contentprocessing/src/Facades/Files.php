<?php

namespace KBox\Documents\Facades;

use Illuminate\Support\Facades\Facade;
use KBox\Documents\Services\FileService;

/**
 * Facade to access the FileService
 */
class Files extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return FileService::class;
    }
}
