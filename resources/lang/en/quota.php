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
    'not_enough_free_space' => 'Not enough free space available (:free) in your assigned quota (:quota).',

    'notifications' => [
        'limit' => [
            'subject' => 'Your K-Box account is approaching the quota limit',
            'text' => 'You asked to notify in case the used storage reaches the :threshold %. This is now the time.',
        ],
        'full' => [
            'subject' => 'Your K-Box account is almost full',
            'text' => 'We have to inform you that your K-Box account as reached its storage limit.',
        ],
    ],

];
