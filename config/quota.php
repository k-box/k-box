<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage quota available for each user (in bytes)
    |--------------------------------------------------------------------------
    |
    | Default storage quota available for each user in bytes
    |
    | possible values:
    | - null: unlimited storage
    | - <= 0: blocks the upload
    | - > 0: the granted storage space will be the exact amount of bytes
    |
    | default: null (unlimited)
    |
    | @var integer|null
    */
    'user' => env('KBOX_DEFAULT_USER_STORAGE_QUOTA', null),

    /*
    |--------------------------------------------------------------------------
    | Threshold for notifications
    |--------------------------------------------------------------------------
    |
    | Threshold of used space to determine if a notification needs
    | to be sent to the user.
    |
    | @var integer Percentage of used space
    */
    'threshold' => env('KBOX_DEFAULT_STORAGE_QUOTA_THRESHOLD_NOTIFICATION', 80),
];
