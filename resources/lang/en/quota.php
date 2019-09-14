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

    'menu_label' => 'Storage',
    'page_title' => 'Manage storage',

    'notifications' => [
        'limit' => [
            'subject' => 'Your K-Box account is approaching the quota limit',
            'text' => 'Your used storage has reached the :threshold% of the available space.',
        ],
        'full' => [
            'subject' => 'Your K-Box account is almost full',
            'text' => 'We are to inform you that your K-Box account as reached its storage limit (:quota).',
        ],
    ],

    'unlimited' => 'Unlimited',

    'unlimited_label' => 'You have unlimited storage',


    'threshold' => [
        'section' => 'Used storage notification',
        'hint' => 'Configure the percentage of used storage after which you would like to receive a notification',
        'update_btn' => 'Update threshold',
        
        'acceptable_value' => 'minimum 5, maximum 98',

        'updated' => 'Notification threshold changed to :threshold%.',
        'not_updated_unlimited' => 'Notification threshold not changed as storage is unlimited.',
    ],

];
