<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Networks Language Lines
    |--------------------------------------------------------------------------
    |
    | contains messages for localizing actions on different public networks
    |
    | original strings taken from
    | - actions.make_public
    | - actions.publish_documents
    | - documents.bulk.making_public_title
    | - documents.bulk.making_public_text
    | - documents.bulk.make_public_error
    | - documents.bulk.make_public_error_title
    | - documents.bulk.make_public_success_text_alt
    | - documents.bulk.make_public_success_title
    | - documents.bulk.make_public_change_title_not_available
    | - documents.bulk.make_public_all_collection_dialog_text
    | - documents.bulk.make_public_inside_collection_dialog_text
    | - documents.bulk.make_public_dialog_title
    | - documents.bulk.make_public_dialog_title_alt
    | - documents.bulk.publish_btn
    | - documents.bulk.make_public_empty_selection
    | - documents.bulk.make_public_dialog_text
    | - documents.bulk.make_public_dialog_text_count
    |
    |
    */

    'klink_network_name' => 'Сеть K-Link',
    'menu_public_klink' => 'Сеть K-Link',
    
    'menu_public' => ':network',
    'menu_public_hint' => 'Узнайте какие документы доступны в :network',

    'make_public' => 'Опубликовать',
    'publish_to_short' => 'Опубликовать',
    'publish_to_long' => 'Опубликовать доступ в :network',

    
    'publish_to_hint' => 'Выберите документы для предоставления к ним доступа в :network',
    

    'publish_btn' => 'Опубликовать',

    'settings' => [
        'section' => 'Присоединиться к сети',
        'section_help' => 'Настройки доступа к сети',
        'enabled' => 'Разрешить публикацию документов',
        'debug_enabled' => 'Разрешить отыскание и устранение неполадок в соединении',
        'username' => 'Имя пользователя, используемое для удостоверенности при соединении',
        'password' => 'Пароль, используемый при соединении',
        'url' => 'Адрес URL Точки Входа в Сеть',
        'name_en' => 'Английская версия',
        'name_ru' => 'Русская версия',
        'name_section' => 'Название сети',
        'name_section_help' => 'Добавьте название сети, которое должно отображаться при публикации. По умолчанию будет использовано "Открытая сеть K-Link"',
        'streaming_section' => 'Видео стриминг',
        'streaming_section_help' => 'Установите видео стриминг для публикации видео в сети',
        'streaming_service_url' => 'Адресная строка видео стриминга',
    ],

    'made_public' => ':num документ опубликован в :network|:num документа опубликовано в :network.|:num документов опубликовано в :network.',
        
    'make_public_error' => 'Операция предоставления открытого доступа не была завершена в связи с ошибкой. :error',
    'make_public_error_title' => 'Невозможно добавить в :network',
    
    'make_public_success_text_alt' => 'Документы находятся в открытом доступе в :network',
    'make_public_success_title' => 'Публикация успешно завершена',

    'making_public_title' => 'Предоставляю открытый доступ в :network...',
    'making_public_text' => 'Пожалуйста, подождите, пока ваши документы публикуются в :network',

    'make_public_change_title_not_available' => 'Изменение названия до публикации файла недоступно.',

    'make_public_all_collection_dialog_text' => 'Вы сделаете все документы данной коллекции открытыми для :network',
    'make_public_inside_collection_dialog_text' => 'Вы сделаете все документы коллекции  ":item" открытыми для :network',
    
    'make_public_dialog_title' => 'Опубликовать ":item" в :network',
    'make_public_dialog_title_alt' => 'Опубликовать в :network',
    
    
    'make_public_empty_selection' => 'Пожалуйста, выберите документы для публикации в :network.',
        
    'make_public_dialog_text' => 'Документ ":item" будет в открытом доступе в :network',
    'make_public_dialog_text_count' => 'Вы сделаете :count документов открытыми в :network',

];
