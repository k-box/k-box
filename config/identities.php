<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OAuth Identity Providers
    |--------------------------------------------------------------------------
    |
    | This file control which OAuth provider is enabled for user
    | log in and registration. By default the feature is
    | disabled. Set the providers value to a comma
    | separated list of provider names to enable.
    |
    */

    'providers' => env('KBOX_IDENTITIES_PROVIDERS', null),

    'supported' => [
        'gitlab',
        'dropbox',
    ],

];
