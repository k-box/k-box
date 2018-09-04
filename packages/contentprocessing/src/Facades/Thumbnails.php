<?php

namespace KBox\Documents\Facades;

use Illuminate\Support\Facades\Facade;
use KBox\Documents\Services\ThumbnailsService;
use KBox\Documents\Testing\Fakes\ThumbnailsServiceFake;

/**
 * Thumbnail facade to access the thumbnails service
 *
 * @see \KBox\Documents\Services\ThumbnailsService
 */
class Thumbnails extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new ThumbnailsServiceFake(static::getFacadeApplication()));
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ThumbnailsService::class;
    }
}
