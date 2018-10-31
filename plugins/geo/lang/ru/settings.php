<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Language Lines for the settings page
    |--------------------------------------------------------------------------
    */

    'page_title' => 'Настройки географического расширения K-Box',

    'description' => 'Это страница настроек географического расширения',

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
        'title' => 'Провайдеры карт',
        'description' => 'Настройте провайдеров базовых карт для визуализации',

        'provider_created' => 'Провайдер ":name" создан',
        'provider_updated' => 'Провайдер ":name" обновлен',
        'default_provider_updated' => '":name" задан как основной',
        'providers_enabled' => 'Провайдеры не включены|{1}Провайдер ":name" активен|[2,*]Включен ":name" и :count других',
        'providers_disabled' => 'Нет отключенных провайдеров|{1}Провайдер ":name" отключен|[2,*]Отключен ":name" и :count других',
        'provider_deleted' => 'Провайдер ":name" удален',
        'provider_delete_denied_is_default' => 'Нельзя удалить основного провайдера ":name"',

        'create_title' => 'Создать провайдера',
        'create_description' => 'Создать новую карту',

        'edit_title' => 'Изменить ":name"',
        'edit_description' => 'Изменить провайдера',

        'types' => [
            'tile' => 'Плиточная карта',
            'wms' => 'Web Map Service (WMS)',
        ],

        'attributes' => [
            'id' => 'id',

            'default' => 'Основной',
            'enabled' => 'Активные',

            'subdomains' => 'Поддомены',
            'subdomains_description' => 'В случае плиточных карт, для повышения скорости загрузки, плитки могут обслуживаться с разных доменов. Обычно это обозначено {s} в URL.',

            'type' => 'Тип провайдера',
            'type_description' => 'Плиточная карта или Web Map Service (WMS)',

            'label' => 'Название',
            'label_description' => 'Название должно быть уникальным среди остальных провайдеров',

            'url' => 'URL',
            'url_description' => 'URL карты',

            'attribution' => 'Атрибуция',
            'attribution_description' => 'Информация для показа пользователям. Обычно это включает информацию об авторских правах',

            'maxZoom' => 'Максимальное приближение',
            'maxZoom_description' => 'Максимальное приближение доступное для этой карты',
            
            'layers' => 'Слои',
            'layers_description' => 'Доступно только для Web Map Services',
        ],
    ],
        
    'validation' => [
        'url' => [
            'regex' => 'Начните URL с http:// или https://, например, https://tile.openstreetmaps.com/{x}/{y}/{z}.png',
        ],
        'label' => [
            'not_in' => "Имя [:label] уже занято. Оно должно быть уникальным",
        ],
        'id' => [
            'not_found' => 'Провайдер не найден',
        ],
        'type' => [
            'not_changeable' => "Тип провайдера [:current] не может быть изменен на [:new]",
        ],
        'default_map' => [
            'in' => 'Выбранная карта недоступна в системе',
        ]
    ],
    
];
