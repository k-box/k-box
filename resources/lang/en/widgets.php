<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Widgets Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the widgets will show aggregate
    | information on the dashboards
    |
    */

    'view_all' => 'View all',

    'dms_admin' => [

        'title'=>'DMS Administration',

    ],

    'starred' => [

        'title'=>'Last Starred',
        'empty' => 'No Starred, start searching for something and mark as starred',

    ],

    'storage' => [

        'title'=>'Storage',
        'free' => '<span class="free">:free</span> of :total free',
        'used' => ':used of :total used',
        'used_alt' => ':used of :total',
        'used_percentage' => ':used% used',

        'graph_labels' => [
            'documents' => 'Documents',
            'images' => 'Images',
            'videos' => 'Videos',
            'other' => 'Other'
        ],

    ],
    
    'user_sessions' => [

        'title'=>'Active Users',
        'empty' => 'No user activity in the last 20 minutes'

    ],

    'recent_searches' => [

        'title'=>'Recent Searches',
        'executed' => 'executed',
        'empty' => 'No past searches',

    ],

    'search_statistics' => [

        'found'=>'document found|documents found',
        'in' => 'in :time :unit',

    ],

];
