<?php

return [

    /*
    |--------------------------------------------------------------------------
    | K-Link DMS Version (aka Application version)
    |--------------------------------------------------------------------------
     */
    'version' => 'BUILDVERSION',
    
    'build' => 'BUILDCODE',
    
    /*
    |--------------------------------------------------------------------------
    | K-Link DMS Edition. Used when upgrading for a version to a new version
    |--------------------------------------------------------------------------
     */
    'edition' => 'project',

    /*
    |--------------------------------------------------------------------------
    | DMS Identifier
    |--------------------------------------------------------------------------
    |
    | The unique identifier for the DMS instance
    |
    | @var string
    | @deprecated
    */

    'identifier' => '4815162342',

    /*
    |--------------------------------------------------------------------------
    | Institution Identifier
    |--------------------------------------------------------------------------
    |
    | The institution identifier that is required for communicating with the
    | K-Link Core
    |
    | @var string
    | @deprecated
    */

    'institutionID' => env('DMS_INSTITUTION_IDENTIFIER', 'KLINK'),

    /*
    |--------------------------------------------------------------------------
    | Guest K-Link searches
    |--------------------------------------------------------------------------
    |
    | Tell if the K-Box will allows K-Link search for non-logged in users
    |
    | @var boolean
    */
    'are_guest_public_search_enabled' => env('KBOX_ENABLE_GUEST_NETWORK_SEARCH', env('DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED', false)),

    'hide_projects_if_empty' => env('KBOX_HIDE_PROJECTS_IF_EMPTY', false),

    'core' => [

        /*
        |--------------------------------------------------------------------------
        | K-Link K-Search URL
        |--------------------------------------------------------------------------
        |
        | The url of the Private K-Search
        |
        | @var string
        */

        'address' => env('KBOX_SEARCH_SERVICE_URL', env('DMS_CORE_ADDRESS', null)),

    ],

    /*
    |--------------------------------------------------------------------------
    | Number of items to display per page
    |--------------------------------------------------------------------------
    |
    |
    | default: 12
    |
    | @var integer
    */

    'items_per_page' => env('KBOX_PAGE_LIMIT', env('DMS_ITEMS_PER_PAGE', 12)),

    /*
    |--------------------------------------------------------------------------
    | Upload folder
    |--------------------------------------------------------------------------
    |
    | Where the files will be uploaded
    |
    | default: /storage/uploads
    |
    | @var string
    */

    'upload_folder' => env('DMS_UPLOAD_FOLDER', storage_path('documents/')),

    /*
    |--------------------------------------------------------------------------
    | File Upload Maximum size (in KB)
    |--------------------------------------------------------------------------
    |
    | The maximum size of the file allowed for upload in kilobytes
    |
    | default: 204800
    |
    | @var integer
    */

    'max_upload_size' => env('KBOX_UPLOAD_LIMIT', env('UPLOAD_LIMIT', env('DMS_MAX_UPLOAD_SIZE', 204800))),

    /*
    |--------------------------------------------------------------------------
    | Allowed File Upload types
    |--------------------------------------------------------------------------
    |
    | A comma separated list of allowed file types to be uploaded
    |
    | default: docx,doc,xlsx,xls,pptx,ppt,pdf,txt,jpg,gif,png,odt,odp,ods
    |
    | @var string
    */

    'allowed_file_types' => getenv('DMS_ALLOWED_FILE_TYPES') ?: 'docx,doc,xlsx,xls,pptx,ppt,pdf,txt,jpg,gif,png,odt,odp,ods,md,txt,rtf,kmz,kml,gdoc,gslides,gsheet',

    /*
    |--------------------------------------------------------------------------
    | Use HTTPS as default url schema
    |--------------------------------------------------------------------------
    |
    | Use HTTPS for serving the pages
    |
    | default: true
    |
    | @var boolean
    */

    'use_https' => starts_with(env('APP_URL'), 'https') ? true : false,
    
    /*
    |--------------------------------------------------------------------------
    | Language whitelist
    |--------------------------------------------------------------------------
    |
    | Use this option to limit the  usable language filters
    |
    | default: 'en', 'de', 'it', 'fr', 'ky', 'ru', 'tg'
    | acceptable value: array of iso_639_1 language codes
    |
    | if configured via environment variable, the acceptable value
    | is a string of comma separated iso_639_1 language codes
    | e.g. en,ru,de
    |
    | Set to null or empty array if the whitelist should be disabled
    |
    | @var array
    */
    
    'language_whitelist' => array_from(env('KBOX_LANGUAGE_WHITELIST', env('DMS_LIMIT_LANGUAGES_TO', ['en', 'de', 'it', 'fr', 'ky', 'ru', 'tg']))),
    
    /*
    |--------------------------------------------------------------------------
    | Recent section personalization
    |--------------------------------------------------------------------------
    */

    'recent' => [

        /*
        |--------------------------------------------------------------------------
        | The maximum number of elements in the recent list
        |--------------------------------------------------------------------------
        |
        | Use this option to limit the number of documents that can be showed
        | in the recent documents
        |
        | default: 500
        |
        | @var int
        */

        'limit' => getenv('DMS_RECENT_LIMIT') ?: 500,

        /*
        |--------------------------------------------------------------------------
        | The maximum number of weeks to consider a document recent
        |--------------------------------------------------------------------------
        |
        | Use this option to limit how old a document could be in order to be
        | considered a recent document.
        | Unit: weeks. A document updated more than 3 weeks before the current date
        | will not be considered recent
        |
        | default: 3 weeks
        |
        | @var int
        | @deprecated
        */

        'time_limit' => getenv('DMS_RECENT_TIMELIMIT') ?: 3,

    ],
];
