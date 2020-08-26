<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Document and Document Descriptor Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for localizing the document description
    | meta information and the document administration menu and title
    |
    */

    'descriptor' => [
        
        'name' => 'Название',
        'added_by' => 'Добавлен',
        'language' => 'Язык',
        'added_on' => 'Добавлен',
        'last_modified' => 'Изменен',
        'indexing_error' => 'Документ не был индексирован в K-Link сети',
        'private' => 'Закрытые',
        'shared' => 'Совместный доступ',
        'is_public' => 'Открытый доступ',
        'is_public_description' => 'Документ доступен для других организаций в K-Link сети',
        'trashed' => 'Документ находится в корзине',
        'klink_public_not_mine' => 'Нельзя внести изменения. Файл является ссылкой на документ, находящийся в открытом доступе в K-Link сети.',
        'undisclosed_user' => '[undisclosed user]',
        'undisclosed_user_hint' => 'Пользователь не захотел раскрывать свое имя'
    ],

    'page_title' => 'Документы',

    'menu' => [
        'all' => 'Все',
        'public' => 'K-Link сеть',
        'private' => 'Закрытые',
        'personal' => 'Мои загрузки',
        'starred' => 'Избранное',
        'shared' => 'Совместные',
        'recent' => 'Недавние',
        'trash' => 'Корзина',
        'not_indexed' => 'Неиндексированные',
        'recent_hint' => 'Последние измененные файлы',
        'starred_hint' => 'Здесь показаны важные и интересные вам документы',
    ],
        'sort' => [
        'sorted_by' => 'Сортирован по :sort',
        'type_project_name' => 'Проектное название',
        'type_search_relevance' => 'Релевантность поиска',
        'type_updated_at' => 'Дата обновления',
        ],
        
        'filtering' => [
        'date_range_hint' => 'Предпочитаемый временной диапазон',
        'items_per_page_hint' => 'Количество элементов на странице',
        'today' => 'Сегодня',
        'yesterday' => 'Со вчерашнего дня',
        'currentweek' => 'Последние 7 дней',
        'currentmonth' => 'Последние 30 дней',
    ],

    'visibility' => [
        'public' => 'Открытые',
        'private' => 'Закрытые',
    ],

    'type' => [
        // See here for better understanding of the russian translation rules https://github.com/symfony/symfony/issues/8698
        // 'нет яблок|есть одно яблоко|есть %count% яблока|есть %count% яблок'
        // no apples | have one apple | have %count% apples | have %count% apples
        // 0 | 1-4 | 5+
        // 21 | 22-24 | 25+
        /**

        - If the number is 1, or the number ends in the word 1 (example: 1, 21, 61) (but not 11), then you should use the first case
        - If the number, or the last digit of the number is 2, 3 or 4, (example: 22, 42, 103, 4) (but not 12, 13 & 14), then you should use the second case
        - If the number ends in any other digit you should use the 3rd case. All the 'teens'  fit in to this catagory (11, 12, 13, 14, 15,16,17,18,19). Any number ending with 0 (including 0 itself) also fits into this category
*/
        'web-page' => 'веб страница|веб страницы|веб страниц',
        'document' => 'документ|документа|документов',
        'spreadsheet' => 'таблица|таблицы|таблиц',
        'presentation' => 'презентация|презентации|презентаций',
        'uri-list' => 'URL список|URL списка|URL списков',
        'image' => 'рисунок|рисунка|рисунков',
        'geodata' => 'геоданные|геоданных|геоданных',
        'text-document' => 'текстовый документ|текстовых документа|текстовых документов',
        'video' => 'видео|видео|видео',
        'archive' => 'архив|архива|архивов',
        'PDF' => 'PDF|PDF|PDF',
        'binary' => 'Двоичный файл|Двоичных файла|Двоичных файлов',
        'audio' => 'Аудиофайл|Аудиофайла|Аудиофайлов',
    ],

    'empty_msg' => 'Нет документов в <strong>:context</strong>',
    'empty_msg_recent' => 'Нет документов для <strong>:range</strong>',

    'bulk' => [

        'removed' => ':num документ перемещен в корзину.|:num документа перемещены в корзину.|:num документов перемещены в корзину.',
        
        'permanently_removed' => ':num документ удален.|:num документа удалены.|:num документов удалены.',
        
        'restored' => ':num документ восстановлен.|:num документа восстановлены.|:num документов восстановлены.',

        'remove_error' => ':error',
        
        'copy_error' => 'Невозможно копировать в коллекцию. :error',
        
        'copy_completed_all' => 'Все документы были добавлены в :collection',
        
        // used when not all the documents you were adding to a collection has been added
        'copy_completed_some' => '{0}Ни один документ не был добавлен, т.к. они уже хранились в ":collection"|[1,Inf] Добавленых документов :count, оставшиеся :remaining уже находились в :collection',
        
        'restore_error' => 'Невозможно восстановить документ. :error',
        

        'adding_title' => 'Добавление документов...',
        'adding_message' => 'Пожалуйста, подождите, ваши документы добавляются в коллекцию...',
        'added_to_collection' => 'Добавлено',
        'some_added_to_collection' => '{0}Не добавлено|[1,Inf]Некоторые документы не были добавлены',
        
        'add_to_error' => 'Невозможно добавить в коллекцию',
        
    ],

    'create' => [
        'page_breadcrumb' => 'Создать',
        'page_title' => 'Создать новый документ',
    ],

    'edit' => [
        'page_breadcrumb' => 'Изменить :document',
        'page_title' => 'Изменить :document',

        'title_placeholder' => 'Название документа',

        'abstract_label' => 'Краткое содержание',
        'abstract_placeholder' => 'Краткое содержание',
        'abstract_help' => 'Поддерживаемый формат  <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank" rel="noopener">Markdown</a> format supported',
        'authors_label' => 'Авторы',
        'authors_help' => 'Авторы должны быть указаны через запятую со следующим форматом <code>имя фамилия &lt;mail@something.com&gt;</code>',
        'authors_placeholder' => 'Авторы документа (имя фамилия <mail@something.com>)',
        
        'language_label' => 'Язык',

        'last_edited' => 'Изменен <strong>:time</strong>',
        'created_on' => 'Создан <strong>:time</strong>',
        'uploaded_by' => 'Загружен <strong>:name</strong>',

        'public_visibility_description' => 'Данный документ будет доступен для всех участников K-Link сети',
        
        
        'not_index_message' => 'Документ недоступен в сети K-Link. Пожалуйста, попробуйте <button type="submit">переиндексировать его</button> сейчас или свяжитесь с вашим администратором.',
        'not_fully_uploaded' => 'Загрузка документа еще не закончилась.',
        'preview_available_when_upload_completes' => 'Просмотр документа будет доступен по завершении его загрузки.',
   
   
        'license' => 'Лицензия',
        'license_help' => 'Указание лицензии дает возможность другим людям использовать данную работу, соблюдая авторские права',
        'license_choose_help_button' => 'Подсказка по выбору лицензии',
        
        'copyright_owner' => 'Владелец авторских прав',
        'copyright_owner_help' => 'Информация о владельце авторских прав. Эта информация указывается независимо от выбранной лицензии.',
        
        'copyright_owner_name_label' => 'ФИО',
        'copyright_owner_email_label' => 'Электронная почта',
        'copyright_owner_website_label' => 'Веб-сайт',
        'copyright_owner_address_label' => 'Адрес',

    
    ],

    'update' => [
        'error' => 'Невозможно обновить этот документ. :error',
        
        'removed_from_title' => 'Снято',
        'removed_from_text' => 'Документ был снят с ":collection"',
        'removed_from_text_alt' => 'Документ был снят с коллекции',
        
        'cannot_remove_from_title' => 'Невозможно снять с коллекции',
        'cannot_remove_from_general_error' => 'Невозможно снять с коллекции. При повторении ошибки, пожалуйста, сообщите Администратору K-Box.',

    ],
    
    'restore' => [
        
        'restore_dialog_title' => 'Восстановить :document?',
        'restore_dialog_text' => 'Вы собираетесь восстановить ":document"',
        'restore_version_dialog_text' => 'Вы собираетесь восстановить версию ":document". Последние загруженные версии будут безвозвратно удалены.',
        'restore_dialog_title_count' => 'Восстановить :count документов?',
        'restore_dialog_text' => 'Вы собираетесь восстановить ":document"',
        'restore_dialog_text_count' => 'Вы собираетесь восстановить :count элементов',
        'restore_dialog_yes_btn' => 'Да',
        'restore_dialog_no_btn' => 'Нет',
        
        'restore_success_title' => 'Готово',
        'restore_error_title' => 'Не восстановлено',
        'restore_error_text_generic' => 'Документ не был перемещен из корзины',
        'restore_version_error_text_generic' => 'Восстановление данной версии не удалось',
        'restore_version_error_only_one_version' => 'Текущая версия является единственной',
      
        'restoring' => 'Восстанавливаю...',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Переместить ":document" в корзину?',
        'dialog_title_alt' => 'Переместить документ в корзину?',
        'dialog_title_count' => 'Переместить :count документов в корзину?',
        'dialog_text' => 'Вы собираетесь переместить :document в корзину.',
        'dialog_text_count' => 'Вы собираетесь переместить :count документов в корзину',
        'deleted_dialog_title' => ':document в корзине',
        'deleted_dialog_title_alt' => 'Готово',
        'cannot_delete_dialog_title' => 'Невозможно переместить ":document"',
        'cannot_delete_dialog_title_alt' => 'Невозможно переместить',
        'cannot_delete_general_error' => 'Произошла ошибка. Пожалуйста, свяжитесь с Администратором.',
    ],

    'permanent_delete' => [
        
        'dialog_title' => 'Удалить безвозвратно ":document"?',
        'dialog_title_alt' => 'Удалить безвозвратно документ?',
        'dialog_title_count' => 'Удалить :count документ?|Удалить :count документа?|Удалить :count документов?',
        'dialog_text' => 'Вы собираетесь безвозвратно удалить :document. Данное действие является необратимым.',
        'dialog_text_count' => 'Вы собираетесь безвозвратно удалить :count документ.Данное действие является необратимым.|Вы собираетесь безвозвратно удалить :count документа. Данное действие является необратимым.|Вы собираетесь безвозвратно удалить :count документов. Данное действие является необратимым.',
        'deleted_dialog_title' => ':document удален',
        'deleted_dialog_title_alt' => 'Готово',
        'cannot_delete_dialog_title' => 'Невозможно удалить ":document"',
        'cannot_delete_dialog_title_alt' => 'Невозможно',
        'cannot_delete_general_error' => 'Произошла ошибка при удалении. Пожалуйста, свяжитесь с вашим Администратором.',
    ],

    'preview' => [
        'page_title' => 'Предварительный просмотр :document',
        'error' => 'Невозможно загрузить предварительный просмотр ":document".',
        'not_available' => 'Предварительный  просмотр невозможен для данного документа.',
        'not_supported' => 'Предварительный просмотр для этого файла недоступен. Формат файла в настоящее время не поддерживается.',
        'google_file_disclaimer' => ':document это файл с Google Диска, поэтому вы не можете просмотреть его здесь. Откройте его на Google Диске.',
        'google_file_disclaimer_alt' => 'Это файл с Google Диска. Просмотр недоступен.',
        'open_in_google_drive_btn' => 'Открыть в Google Диске',
        'video_not_ready' => 'Видео в процессе обработки и будет доступно через несколько секунд.',
        'file_not_ready' => 'K-Box обрабатывает файл. Во время обработки файла предварительный просмотр недоступен, повторите попытку позже.',
        
    ],

    'versions' => [

        'section_title' => 'Версии',

        'section_title_with_count' => ':number версия|:number версии|:number версий',

        'version_count_label' => ':number версия|:number версии|:number версий',

        'version_number' => 'версия :number',

        'version_current' => 'текущая',

        'new_version_button' => 'Загрузить новую версию',
        
        'new_version_button_uploading' => 'Загрузка...',

        'filealreadyexists' => 'Версия загружаемого вами документа уже существует в системе',
    ],

    'messages' => [
        'updated' => 'Документ обновлен',
        'processing' => 'Документ обратывается K-Box. Отображение в результатах поиска будет замедлено до окончания процесса.',
        'local_public_only' => 'В настоящее время показаны только открытые документы организации.',
        'forbidden' => 'У вас нет пользовательских прав для изменения документа.',
        'delete_forbidden' => 'Вы не можете удалять документы. Пожалуйста, обратитесь к Администратору проекта.',
        'delete_public_forbidden' => 'Вы не можете удалить документ, находящийся в открытом доступе. Пожалуйста, обратитесь к Администратору проекта.',
        'delete_force_forbidden' => 'Вы не можете безвозвратно удалить документ. Пожалуйста, обратитесь к Администратору проекта.',
        'drag_hint' => 'Перетащите файлы сюда для начала загрузки.',
        'recent_hint_dms_manager' => 'Все обновления файлов в доступных проектах',
        'no_documents' => 'Нет документов. Вы можете загрузить новые документы с помощью перетаскивания или "Создать или добавить" в верхнем навигационном поле.',
    ],
    
     'trash' => [
        
        'clean_title' => 'Очистить корзину?',
        'yes_btn' => 'Да',
        'no_btn' => 'Нет',
        
        'empty_trash' => 'Корзина пуста',
        
        'empty_all_text' => 'Все документы будут безвозвратно удалены из корзины.',
        'empty_selected_text' => 'Вы собираетесь безвозвратно удалить из корзины выбранные документы. Вы также удалите файлы, версии, избранное, коллекции и совместные доступы.',
        
        'cleaned' => 'Корзина очищена',
        'cannot_clean' => 'Невозможно очистить корзину',
        'cannot_clean_general_error' => 'Произошла ошибка. При повторении ошибки, пожалуйста, свяжитесь с Администратором проекта.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Ваш браузер не поддерживает перетаскивание папок.',
        'error_dialog_title' => 'Ошибка загрузки',
        
        'max_uploads_reached_title' => 'Загрузка...',
        'max_uploads_reached_text' => 'Мы можем обрабатывать пока только маленькие файлы. Пожалуйста, проявите немного терпения перед очередным добавлением файлов.',
        
        'all_uploaded' => 'Все файлы были успешно загружены',
        
        'upload_dialog_title' => 'Загрузка',
        'page_title' => 'Загрузка',
        'dragdrop_not_supported' => 'Ваш браузер не поддерживает функцию загрузки файлов с помощью перетаскивания.',
        'dragdrop_not_supported_text' => 'Пожалуйста, используйте выбор файлов в "Создать или добавить".',
        'remove_btn' => "В корзину",
        'cancel_btn' => 'Отменить загрузку',
        'cancel_question' => 'Вы уверенны, что хотите отменить загрузку?',
        'outside_project_target_area' => 'Пожалуйста перетащите ваш файл в проект для его загрузки.',
        'empty_file_error' => 'Пустой документ. Пожалуйста, загрузите файл с содержанием одного слова и больше.',
    ],

        'duplicates' => [
        'badge' => 'Копия',
        'duplicates_btn' => 'Копии',
        'duplicates_btn_hint' => 'Управление копиями',
        'duplicates_description' => 'Копия документа:',

        'in_trash' => 'в Корзине',

        'message_me_owner' => 'Документ <a href=":duplicate_link" target="_blank" rel="noopener noreferrer">:duplicate_title</a>  - копия <a href=":existing_link" target="_blank" rel="noopener noreferrer">:existing_title</a>.',
        'message_with_owner' => 'Документ <a href=":duplicate_link" target="_blank" rel="noopener noreferrer">:duplicate_title</a> - копия <a href=":existing_link" target="_blank" rel="noopener noreferrer">:existing_title</a>.',
        'message_in_collection' => 'Документ <a href=":duplicate_link" target="_blank" rel="noopener noreferrer">:duplicate_title</a> - копия <a href=":existing_link" target="_blank" rel="noopener noreferrer">:existing_title</a> (коллекцию :collections).',
        
        'resolve_duplicate_button' => 'Удалить мою копию',

        'processing' => 'Удаление копии...',

        'errors' => [
            'title' => 'Не удалось удалить копию',
            'generic' => 'Не удалось удалить копию',
            'already_resolved' => 'Ошибка с копиями решена',
            'resolve_with_trashed_document' => 'Ошибка с копиями решена',
        ],
    ],

];
