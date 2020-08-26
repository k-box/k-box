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

    'view_all' => 'Показать все',

    'dms_admin' => [

        'title'=>'Панель управления K-Box',

    ],

    'starred' => [

        'title'=>'Последние Избранные',
        'empty' => 'Избранных документов нет. Начните поиск и отметьте звездочкой важный или интересный вам документ',

    ],

    'storage' => [

        'title'=>'Статус хранения',
        'free' => '<span class="free">:free</span> свободных из :total',
        'used' => ':used использовано из :total',
        'used_alt' => ':used использовано :total',
        'used_percentage' => ':used% использовано',
        'used_single' => ':used использовано',
    
        'graph_labels' => [
            'documents' => 'Документы',
            'images' => 'Картинки',
            'videos' => 'Видео',
            'other' => 'Другие'
        ],
 
    ],
    
    'user_sessions' => [

        'title'=>'Пользователи в системе',
        'empty' => 'В последние 20 минут действий со стороны пользователей не совершено'

    ],

    'recent_searches' => [

        'title'=>'Недавно совершенные поиски',
        'executed' => 'Выполненные',
        'empty' => 'Недавно совершенных поисков нет',

    ],

    'search_statistics' => [

        'found'=>'документ найден|документа найдено|документов найдено',
        'in' => 'в :time :unit',

    ],

];
