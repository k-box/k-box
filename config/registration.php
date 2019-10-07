<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable User Registration
    |--------------------------------------------------------------------------
    |
    | Let the K-Box accept user registrations
    |
    | @var boolean
    | @default false
    */
    'enable' => env('KBOX_USER_REGISTRATION', false),
    
    /*
    |--------------------------------------------------------------------------
    | Require invite
    |--------------------------------------------------------------------------
    |
    | Require an invite to let user create an account
    |
    | @var boolean
    | @default false
    */

    'invite_required' => env('KBOX_USER_REGISTRATION_INVITE_ONLY', false),

];
