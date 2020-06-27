<?php

namespace KBox;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMember extends Pivot
{
    protected $table = 'userprojects';
    
    public $incrementing = true;
}
