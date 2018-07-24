<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ...
    |--------------------------------------------------------------------------
    |
    | ...
    |
    | @var string
    */
    'url' => 'geoserver.test.oneofftech.xyz',

    'default' => env('KBOX_GEOSERVER_CONNECTION', 'local'),

    'connections' => [
        'local' => [
            'driver'    => 'local',
            'url'       => env('KBOX_GEOSERVER_URL', 'https://geoserver.test.oneofftech.xyz/geoserver/'),
            'username'  => env('KBOX_GEOSERVER_USER', 'admin'), 
            'password'  => env('KBOX_GEOSERVER_PASS', 'geoserver'),
            'workspace' => env('KBOX_GEOSERVER_WORKSPACE', 'kbox'), // the geoserver workspace that needs to be used by the K-Box
        ],
    ] 

];
