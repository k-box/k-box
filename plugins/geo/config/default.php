<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Geo plugin default configuration
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | GeoServer connection configuration
    |--------------------------------------------------------------------------
    */
    
    'geoserver_url' => null,
    
    'geoserver_username' => null,
    
    'geoserver_password' => null,

    'geoserver_workspace' => 'kbox',

    'map' => [

        /*
        |--------------------------------------------------------------------------
        | Map control configuration
        |--------------------------------------------------------------------------
        */

        'default' => 'hum_osm',

        'providers' => [
            "hum_osm" => [
                'label' => "Humanitarian Open Street Maps",
                'url' => 'https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png',
                'type' => 'tile',
                'maxZoom' => 20,
                'subdomains' => "abc",
                'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>',
            ],
            "osm" => [
                'type' => 'tile',
                'label' => "Open Street Maps",
                'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                'maxZoom' => 19,
            ],
            "mundialis_topo" => [
                'label' => "Mundialis (Topographic OSM)",
                'layers' => "TOPO-OSM-WMS",
                'attribution' => '&copy; <a href="https://www.mundialis.de/en/ows-mundialis/" target="_blank">Mundialis GmbH & Co. KG</a>',
                'type' => 'wms',
                'url' => 'http://ows.mundialis.de/services/service?',
            ],
        ]
    ]
    
];
