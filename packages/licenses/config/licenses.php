<?php

/** Configuration file for the license package */

return [
    /*
    |--------------------------------------------------------------------------
    | The licenses file name
    |--------------------------------------------------------------------------
    |
    | The name of the license JSON file that contains all the
    | available licenses
    |
    | default: licenses.json
    |
    | @var string
    */
    'license_collection' => 'licenses.json',

    /*
    |--------------------------------------------------------------------------
    | Data folder
    |--------------------------------------------------------------------------
    |
    | The path of the folder that contains the licenses, the descriptions
    | and the icons.
    |
    | The folder must contain
    | - the license JSON file in the root
    | - the icons folder
    | - the description folder
    |
    | The description folder must contain one subfolder for the supported
    | locales
    |
    | default: ../assets
    |
    | @var string
    */
    'assets' => __DIR__.'/../assets/',
];
