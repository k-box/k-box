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
                'label' => "Humanitarian OpenStreetMap",
                'url' => 'https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png',
                'type' => 'tile',
                'maxZoom' => 18,
                'subdomains' => "abc",
                'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>',
            ],
            "osm" => [
                'type' => 'tile',
                'label' => "OpenStreetMap",
                'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                'maxZoom' => 18,
            ],
            "mundialis_topo" => [
                'label' => "Mundialis (Topographic OSM)",
                'layers' => "TOPO-OSM-WMS",
                'attribution' => '&copy; <a href="https://www.mundialis.de/en/ows-mundialis/" target="_blank">Mundialis GmbH & Co. KG</a>',
                'type' => 'wms',
                'url' => 'http://ows.mundialis.de/services/service?',
            ],
            "sentinel_3857" => [
                'label' => "Sentinel-2 cloudless by EOX",
                'layers' => "s2cloudless_3857,overlay_bright_3857",
                'attribution' => '<a href="https://s2maps.eu">Sentinel-2 cloudless – https://s2maps.eu</a> by <a href="https://eox.at/">EOX IT Services GmbH</a> (Contains modified Copernicus Sentinel data 2016 & 2017)',
                'type' => 'wms',
                'url' => 'https://tiles.maps.eox.at/?',
            ],
            "sentinel_2017_3857" => [
                'label' => "Sentinel-2 cloudless layer for 2017 by EOX",
                'layers' => "s2cloudless-2017_3857",
                'attribution' => '<a href="https://s2maps.eu">Sentinel-2 cloudless – https://s2maps.eu</a> by <a href="https://eox.at/">EOX IT Services GmbH</a> (Contains modified Copernicus Sentinel data 2016 & 2017)',
                'type' => 'wms',
                'url' => 'https://tiles.maps.eox.at/?',
            ],
            "sentinel_terrain_3857" => [
                'label' => "Sentinel Terrain by EOX",
                'layers' => "terrain_3857,overlay_bright_3857",
                'attribution' => '<a href="https://s2maps.eu">Sentinel-2 cloudless – https://s2maps.eu</a> by <a href="https://eox.at/">EOX IT Services GmbH</a> (Contains modified Copernicus Sentinel data 2016 & 2017)',
                'type' => 'wms',
                'url' => 'https://tiles.maps.eox.at/?',
            ],
        ]
    ]
    
];
