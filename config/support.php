<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Support service
    |--------------------------------------------------------------------------
    |
    | The support service to use. The supported values comes from the
    | providers list, for example "uservoice".
    | Set this configuration to null will disable any support service
    |
    */
    'service' => env('KBOX_SUPPORT_SERVICE', env('SUPPORT_SERVICE', null)),
    
    /*
    |--------------------------------------------------------------------------
    | Support service providers
    |--------------------------------------------------------------------------
    |
    | The providers that can be use for serving support requests.
    |
    */
    'providers' => [
        'mail' => [
            'address' => env('KBOX_SUPPORT_MAIL_ADDRESS', null),
        ],
        'uservoice' => [
            'token' => env('KBOX_SUPPORT_USERVOICE_TOKEN', env('SUPPORT_USERVOICE_TOKEN', env('KBOX_SUPPORT_TOKEN', env('SUPPORT_TOKEN', null)))),
        ],
    ],
    
];
