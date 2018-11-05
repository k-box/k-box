<?php

namespace KBox\Pages;

/**
 *
 *
 * @property string $title
 * @property string $description
 * @property array $authors
 * @property string $created_at
 * @property string $updated_at
 * @property
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

    protected $dateFormat = 'Y-m-d H:i:s.u';
}
