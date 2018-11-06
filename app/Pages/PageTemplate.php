<?php

namespace KBox\Pages;

use LogicException;

class PageTemplate extends Page
{
    protected static $disk = 'assets';
    protected static $pathInDisk = 'pages/stubs';

    public function save()
    {
        // Template cannot be changed
        throw new LogicException('Page templates cannot be saved/updated');
    }

    public function delete()
    {
        // Template cannot be deleted
        throw new LogicException('Page templates cannot be deleted');
    }

    public static function create($attributes = [])
    {
        // Template cannot be created
        throw new LogicException('Page templates cannot be created');
    }
}
