<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Language Lines for the settings page
    |--------------------------------------------------------------------------
    */

    'page_title' => 'Настройки географического расширения K-Box',

    'description' => 'Настройки географического расширения',

    'geoserver' => [
        'title' => 'Соединение с GeoServer',
        'description' => 'GeoServer используется для хранения, просмотра и конвертации файлов географических данных',

        'url' => 'URL (например, https://domain.com/geoserver/)',
        'username' => 'Логин',
        'password' => 'Пароль',
        'workspace' => 'Рабочая среда для GeoServer (например, kbox)',
    ],

    'gdal' => [
        'available' => 'Установлен Gdal (:version)',
        'not_available' => 'Gdal недоступен, некоторые функции могут быть недоступны',
    ],

    'connection' => [
        'established' => 'Соединился с GeoServer (:version)',
        'failed' => 'Не соединился с GeoServer :error',
    ],

    'providers' => [
        'title' => 'Поставщики карт',
        'description' => 'Настройте базовые карты',

        'provider_created' => 'Карта ":name" создана',
        'provider_updated' => 'Карта ":name" обновлена',
        'default_provider_updated' => '":name" задана как основная',
        'providers_enabled' => 'Карты не включены|{1}Карта ":name" активна|[2,*]Включена ":name" и :count других',
        'providers_disabled' => 'Нет отключенных карт|{1}Карта ":name" отключена|[2,*]Отключена ":name" и :count других',
        'provider_deleted' => 'Карта ":name" удалена',
        'provider_delete_denied_is_default' => 'Невозможно удалить основную карту ":name". Для этого задайте другую карту как основную.',

        'create_title' => 'Создать карту',
        'create_description' => 'Создать новую карту',

        'edit_title' => 'Изменить ":name"',
        'edit_description' => 'Изменить карту',

        'types' => [
            'tile' => 'Плиточная карта',
            'wms' => 'Web Map Service (WMS)',
        ],

        'attributes' => [
            'id' => 'id',

            'default' => 'Основная',
            'enabled' => 'Активные',

            'subdomains' => 'Поддомены',
            'subdomains_description' => 'В случае плиточных карт, для повышения скорости загрузки, плитки могут обслуживаться с разных доменов. Обычно это обозначено {s} в URL.',

            'type' => 'Тип карты',
            'type_description' => 'Плиточная карта или Web Map Service (WMS)',

            'label' => 'Название',
            'label_description' => 'Название должно быть уникальным среди остальных карт',

            'url' => 'URL',
            'url_description' => 'URL карты',

            'attribution' => 'Атрибуция',
            'attribution_description' => 'Информация для показа пользователям. Обычно это включает информацию об авторских правах',

            'maxZoom' => 'Максимальное приближение',
            'maxZoom_description' => 'Максимальное приближение доступное для этой карты',
            
            'layers' => 'Слои',
            'layers_description' => 'Доступно только для Web Map Services',
        ],

        'validation' => [
            'url' => [
                'regex' => 'Начните URL с http:// или https://, например, https://tile.openstreetmaps.com/{x}/{y}/{z}.png',
            ],
            'label' => [
                'not_in' => "Имя [:label] уже занято. Оно должно быть уникальным.",
            ],
            'id' => [
                'not_found' => 'Поставщик не найден',
            ],
            'type' => [
                'not_changeable' => "Тип поставщика [:current] не может быть изменен на [:new]",
            ],
            'default_map' => [
                'in' => 'Выбранная карта недоступна в системе',
            ]
        ],
    ],
    
];
