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

        'title'=>'Administration DMS',

    ],

    'starred' => [

        'title'=>'Dernier favori',
        'empty' => 'Aucun favori. Cherchez des documents intéressants et marquez les comme favoris',

    ],

    'storage' => [

        'title'=>'Stockage',
        'free' => '<span class="free">:free</span> de :total sont libres',
        'used' => ':used de :total sont utilisés',
        'used_alt' => ':used de :total',
        'used_percentage' => ':used% utilisé',

        'graph_labels' => [
            'documents' => 'Documents',
            'images' => 'Images',
            'videos' => 'Vidéos',
            'other' => 'Autres'
        ],

    ],
    
    'user_sessions' => [

        'title'=>'Utilisateurs actifs',
        'empty' => 'Aucun utilisateur actif durant les dernières 20 minutes'

    ],

    'recent_searches' => [

        'title'=>'Recherches récentes',
        'executed' => 'exécuté',
        'empty' => 'Aucune recherche antérieure',

    ],

    'search_statistics' => [

        'found'=>'document trouvé|documents trouvés',
        'in' => 'en :time :unit',

    ],

];
