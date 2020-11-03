<?php

namespace KBox\Documents\Presentation;

use PhpOffice\PhpPresentation\Shape\Placeholder;

class Placeholders
{
    protected static $placeholders = [
        Placeholder::PH_TYPE_BODY,
        Placeholder::PH_TYPE_CHART,
        Placeholder::PH_TYPE_SUBTITLE,
        Placeholder::PH_TYPE_TITLE,
        Placeholder::PH_TYPE_FOOTER,
        Placeholder::PH_TYPE_DATETIME,
        Placeholder::PH_TYPE_SLIDENUM,
    ];
    
    public static function list()
    {
        return self::$placeholders;
    }
}
