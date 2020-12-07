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
            'a' => 'Oldest first',
            'd' => 'Newest first',
        ],
        'string' => [
            'a' => 'A to Z',
            'd' => 'Z to A',
        ],
        'number' => [
            'a' => 'Smallest first',
            'd' => 'Largest first',
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