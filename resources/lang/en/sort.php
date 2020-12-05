<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sorting Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for presenting sorting options.
    |
    */

    'directions' => [

        // keyed by data type
        // a = ascending
        // d = descending

        'date' => [
            'a' => 'Older to newer',
            'd' => 'Newer to older',
        ],
        'string' => [
            'a' => 'A to Z',
            'd' => 'Z to A',
        ],
        'number' => [
            'a' => 'Smaller to larger',
            'd' => 'Larger to smaller',
        ],
    ],

    'labels' => [
        'update_date' => 'Modified date',
        'name' => 'Name',
        'creation_date' => 'Creation date',
        'type' => 'File type',
        'language' => 'Language',
        'shared_by' => 'Shared by',
        'shared_date' => 'Shared on',
    ],
  
    'button' => 'Sort by',
    'change_direction' => 'Change sorting direction',
];