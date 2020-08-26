<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared page Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'page_title' => 'Документы в совместном доступе',
    
    'share_btn' => 'Поделиться',

    'share_panel_title' => 'Предоставить доступ к :num элементу|Предоставить доступ к :num элементам|Предоставить доступ к :num элементам',
    
    'share_panel_title_alt' => 'Предоставить доступ к ":what"|Предоставить доступ к ":what" и :count другим файлам|Предоставить доступ к ":what" и :count другим файлам',

    'share_created_msg' => ':num доступ создан|:num доступа создано|:num доступов создано',

    'with_label' => 'Предоставить доступ к',

    'what_label' => 'Вы предоставляете совместный доступ к данному файлу',

    'empty_with_me_message' => 'Никто пока не предоставил вам совместного доступа к документам',

    'empty_by_me_message' => 'Вы не предоставили совместного доступа ни к одному документу или коллекции',

    'shared_by_me_title' => 'Предоставленный мною совместный доступ',
    'shared_by_me_count' => 'Предоставлен доступ к :num элементу|:num элементам|:num элементам',

    'shared_with_me_title' => 'Доступные мне',
    
    'shared_with_label' => 'Вами предоставлен доступ к',
    'shared_by_label' => 'Доступ от',
    
    'bulk_destroy' => 'Доступ к документу удален|Совместный доступ к некоторым документам не может быть удален<br/>:errors',
    'removed' => 'Доступ закрыт',
    'remove_error' => 'Невозможно закрыть доступ. :Ошибка',
    'unshare' => 'Отменить совместный доступ',
    'unsharing' => 'Совместный доступ отменяется...',
    'remove' => 'Удалить',
    'removing' => 'Удаляется...',
    
    'share_link_section' => 'Поделиться ссылкой',
    'download_link_copy' => 'Скопировать ссылку для загрузки',
    'document_link_copy' => 'Скопировать ссылку',
    'preview_link_copy' => 'Копировать ссылку на просмотр документа',
    'document_link_copy_multiple' => 'Скопировать ссылки',
    'send_link' => 'Отправить ссылку',
    'send_link_multiple' => 'Отправить ссылки',
    
    'link_copied_to_clipboard' => 'Ссылка скопирована. Вы можете вставить ее, используя комбинацию CTRL+V',

    'shared_on' => 'Дата',
    
    'dialog' => [
        'title' => 'Настройки доступа',
        'subtitle_single' => ':what', // only one element to share
        'subtitle_multiple' => ':what и еще :count файл|:what и еще :count файла|:what и еще :count файлов ', // X and 1 other|X and 2 others
        'share_created' => 'Предоставлено',
        'collection_shared' => 'Совместный доступ к коллекции предоставлен',
        'collection_shared_text' => 'Доступ к коллекции предоставлен',
        'document_shared' => 'Предоставлено',
        'document_shared_text' => 'Доступ к документу предоставлен',
        'multiple_selection_not_supported' => 'Функция выбора нескольких файлов пока недоступна.',
        'publish_multiple_selection_not_supported' => 'Одновременно опубликовать можно только один файл.',
        'publish_collection_not_supported' => 'Функция публикации коллекции пока недоступна.',

        'section_access_title' => 'Статус доступа',
        'section_linkshare_title' => 'Ссылка для копирования',
        'section_linkshare_title_alternate' => 'Ссылка для копирования',
        'section_publish_title' => 'Опубликовать',

        'linkshare_hint' => 'Только зарегистрированные пользователи получат доступ к файлу.',
        'linkshare_multiple_selection_hint' => 'При выборе нескольких файлов доступ можно открыть только для зарегистрированных пользователей. Чтобы предоставить внешний доступ, выберите один документ',
        'linkshare_members_only' => 'Доступ открыт только для зарегистрированных пользователей',
        'linkshare_public' => 'Доступ открыт для любого получателя ссылки',

        'published' => 'Опубликован в :network',
        'not_published' => 'Документ открыт исключительно для внутреннего пользования',
        'publishing' => 'Идет публикация документа...',
        'publishing_failed' => 'Публикация прошла безуспешно',
        'unpublishing' => 'Идет отмена публикации...',
        'publish_collection' => 'Действие коснется всех документов коллекции.',
        'publish_already_in_progress' => 'Публикация документа уже началась',

        'document_is_shared' => 'Имеют доступ:',
        'collection_is_shared' => 'Имеют доступ:',
        'users_already_has_access' => ':num пользователь|:num пользователя|:num пользователей',
        'users_already_has_access_alternate' => '{0}Только вы|{1}:num пользователь|[2,4]:num пользователя|[5,*]:num пользователей',
        
        'users_already_has_access_with_public_link' => '{0}Любой пользователь, получивший открытую ссылку, получит доступ|{1}Вы и пользователи получившие открытую ссылку|[2,*]:num пользователя и люди, получившие открытую ссылку',
        'document_already_accessible_by_all_users' => 'Документ уже доступен для всех пользователей системы.',
        'collection_already_accessible_by_all_users' => 'Коллекция уже открыта для всех пользователей системы.',

        'add_users' => 'Добавить',
        'select_users' => 'Введите имя...',

        'access_by_direct_share' => 'Прямой доступ',
        'access_by_project_membership' => 'Проект ":project"',
        'access_by_project_membership_hint' => 'У вас есть доступ к документу потому что вы участник проекта ":project"',
        'cannot_add_users_because_of_project_collection' => 'Добавляйте новых участников в проект для совместного использования коллекции проектов.',

    ],
    'publiclinks' => [
        'public_link' => 'Открытая ссылка',
        'already_exist' => 'Открытая ссылка к :name уже существует.',
        'delete_forbidden_not_your' => 'Удалить публичную ссылку может только пользователь загрузивший файл',
        'edit_forbidden_not_your' => 'Вы не можете редактировать чужую ссылку.',
    ],
];
