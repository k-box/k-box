<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics token
    |--------------------------------------------------------------------------
    |
    | Analytics service identifier for the application. Some service
    | might call it siteId, monitoring id
    |
    */
    'token' => env('KBOX_ANALYTICS_TOKEN', env('ANALYTICS_TOKEN', null)),

    /*
    |--------------------------------------------------------------------------
    | Analytics service
    |--------------------------------------------------------------------------
    |
    | The analytics service to use. Currently `matomo`
    | or `google-analytics`
    |
    */
    'service' => env('KBOX_ANALYTICS_SERVICE', env('ANALYTICS_SERVICE', 'matomo')),
    
    /*
    |--------------------------------------------------------------------------
    | Analytics services
    |--------------------------------------------------------------------------
    |
    | The services that can be use for tracking analytics.
    | This list maps the selectable service to the view
    | that contains the inclusion code and to custom
    | configuration options
    |
    */
    'services' => [
        'matomo' => [
            'domain' => env('KBOX_ANALYTICS_MATOMO_DOMAIN', env('ANALYTICS_MATOMO_DOMAIN', null)),
            'view' => 'analytics.matomo',
        ],
        'google-analytics' => [
            'view' => 'analytics.google-analytics',
        ],
    ],
    
];
