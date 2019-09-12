<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Quota Language Lines
    |--------------------------------------------------------------------------
    |
    | Language lines used to express user quota related statements
    |
    */

    'exceeded' => 'You have exceeded the storage quota assigned',
    'not_enough_free_space' => 'The file you are trying to upload exceeds :quota quota limit. You need an additional :necessary_free_space to upload this file.',

    'notifications' => [
        'limit' => [
            'subject' => 'Your K-Box account is approaching the quota limit',
            'text' => 'Your used storage has reached the :threshold% of the available space.',
        ],
        'full' => [
            'subject' => 'Your K-Box account is almost full',
            'text' => 'We have to inform you that your K-Box account as reached its storage limit (:quota).',
        ],
    ],

];
