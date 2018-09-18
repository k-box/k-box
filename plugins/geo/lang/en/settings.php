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

        'provider_created' => 'Map Provider ":name" created',
        'provider_updated' => 'Map Provider ":name" updated',

        'create_title' => 'Create Provider',
        'create_description' => 'Create a new map provider',

        'edit_title' => 'Edit provider ":name"',
        'edit_description' => 'Edit a map provider',

        'types' => [
            'tile' => 'Tiled map provider',
            'wms' => 'Web Map Service (WMS) provider',
        ],

        'attributes' => [
            'id' => 'id',

            'default' => 'default',

            'subdomains' => 'Subdomains',
            'subdomains_description' => 'For tile based providers, the tiles can be served from different domains to speed the loading time. In the url this is usually expressed with the {s} placeholder.',

            'type' => 'Map provider type',
            'type_description' => 'If is a tile based map or a map served by a Web Map Service (WMS)',

            'label' => 'Name',
            'label_description' => 'The name to assign to this provider. It must be unique accross all already defined providers',

            'url' => 'Url',
            'url_description' => 'The URL format for loading the map',

            'attribution' => 'Attribution',
            'attribution_description' => 'The attribution string that will be presented to the users when the provider is selected. This usually includes copyright and license notices',

            'maxZoom' => 'Maximum zoom level',
            'maxZoom_description' => 'The maximum zoom level supported by this provider',
            
            'layers' => 'Layers',
            'layers_description' => 'The layers to use, that are given by the map provider. This option applies only to Web Map Services',
        ],

        'validation' => [
            'url' => [
                'regex' => 'The url must start with http:// or https://, e.g. https://tile.openstreetmaps.com/{x}/{y}/{z}.png',
            ],
            'label' => [
                'not_in' => "The provider name must be unique. [:label] is already taken.",
            ],
            'id' => [
                'not_found' => 'Provider cannot be found',
            ],
            'type' => [
                'not_changeable' => "Provider type [:current] cannot be changed to [:new]",
            ],
        ],
    ],
    
];
