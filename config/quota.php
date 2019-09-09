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
    'user_storage_default' => env('KBOX_DEFAULT_USER_STORAGE_QUOTA', null),
];
