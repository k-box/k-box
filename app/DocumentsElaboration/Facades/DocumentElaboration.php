<?php

namespace KBox\DocumentsElaboration\Facades;

use Illuminate\Support\Facades\Facade;
use KBox\DocumentsElaboration\DocumentElaborationManager;
use KBox\DocumentsElaboration\Testing\Fakes\DocumentElaborationFake;

/**

 *
 * @see \KBox\DocumentsElaboration\DocumentElaborationManager
 */
class DocumentElaboration extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new DocumentElaborationFake(static::getFacadeApplication()));
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return DocumentElaborationManager::class;
    }
}
