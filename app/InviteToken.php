<?php

namespace KBox;

use Ramsey\Uuid\Uuid;

class InviteToken
{
    /**
     * Create an invite token
     *
     * @return string
     */
    public static function generate()
    {
        $r = str_replace('-', '', Uuid::uuid4()->toString());
        
        return 'in'.$r;
    }
}
