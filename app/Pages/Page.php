<?php

namespace KBox\Pages;

/**
 * A content page
 *
 */
class Page extends PageModel
{
    protected $casts = [
        'id' => 'string',
        'language' => 'string',
        'title' => 'string',
        'description' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = ['content'];
    protected $appends = ['language'];

    protected $dateFormat = 'Y-m-d H:i:s.u';
}
