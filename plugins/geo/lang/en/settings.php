<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Language Lines for the settings page
    |--------------------------------------------------------------------------
    */

    'page_title' => 'K-Box Geographic Extension Settings',

    'description' => 'This page contains the settings to customize the behavior of the Geographic Extension',

    'geoserver' => [
        'title' => 'GeoServer connection',
        'description' => 'The parameter used to connect to a GeoServer instance. GeoServer is used to store, preview and convert geographic files',

        'url' => 'The address of the geoserver instance (e.g. https://domain.com/geoserver/)',
        'username' => 'The username to authenticate to the GeoServer',
        'password' => 'The password to authenticate to the GeoServer',
        'workspace' => 'The GeoServer workspace to use (e.g. kbox)',
    ],

    'connection' => [
        'established' => 'GeoServer (:version) connection established.',
        'failed' => 'Failed to connect to GeoServer. :error',
    ],

    'providers' => [
        'title' => 'Map Providers',
        'description' => 'Configure the providers of the base maps used for map visualizations',

        'attributes' => [
            'id' => 'id',
            'id_description' => '...',

            'default' => 'default',
            'default_description' => '...',

            'subdomains' => 'subdomains',
            'subdomains_description' => '...',

            'type' => 'type',
            'type_description' => '...',

            'label' => 'label',
            'label_description' => '...',

            'url' => 'url',
            'url_description' => '...',

            'attribution' => 'attribution',
            'attribution_description' => '...',

            'maxZoom' => 'maxZoom',
            'maxZoom_description' => '...',
        ],
    ],
    
];
