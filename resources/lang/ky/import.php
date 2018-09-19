<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Document Import page Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the Import documents page
    |
    */

    'page_title' => 'Импорт',

    'clear_completed_btn' => 'Тазалоо аяктады',

    'import_status_general' => '{0} импортирование завершено| :num импортирование в прогрессе| :num импортирования в прогрессе| :num импортирований в прогрессе',

    'import_status_details' => ':total всего, :completed завершено и :executing в процессе',
    
    'preparing_import' => 'Подготовка к импортированию...',

    'form' => [
        'submit_folder' => 'Папка для импорта',
        'submit_web' => 'Импортировать с веб',

        'select_web' => 'С URL',
        'select_folder' => 'Из папки',

        'placeholder_web' => 'http(s)://названиесайта.com/файл.pdf',
        'placeholder_folder' => '/путь/к/какой-нибудь/папке',

        'help_web' => 'Пожалуйста, введите один url на одну строку. Веб адреса, которым нужна аутентификация, не поддерживаются.',
        'help_folder' => 'Сетевые папки должны находиться на локальной файловой системе, как указано в <a href=":help_page_route" target="_blank">Помощи по импортированию</a>.',

    ],
    
    'status' => [
        // The import is in the queue and waits for being processed
        'queued' => 'В ожидании',
        // The import is put on hold
        'paused' => 'Пауза',
        // The import is downloading the files
        'downloading' => 'Загрузка',
        // The import is completed
        'completed' => 'Завершено',
        // The documents imported are in the search engine indexing phase
        'indexing' => 'Подготовка к поиску',
        // Import has an error
        'error' => 'Ошибка',
    ],

    'remove' => [
        'remove_btn' => 'Удалить',
        'remove_btn_hint' => 'Удалить импорт',
        'remove_dialog_title' => 'Удалить ":import"?',
        'remove_confirmation' => 'Вы хотите удалить ":import"?',
        'removing' => 'Удаление ":import"...',
        'removing_alt' => 'Удаление...',
        'removed_message' => '":import" был удален из списка.',

        // message showed when a user wants to remove an import created by another user
        'destroy_forbidden_user' => 'Вы не можете удалить ":import" из списка из-за отсутствия утверждение обладателя импорта.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'destroy_forbidden_user_alternate' => 'Невозможно удалить импорт, так как вы не являетесь его обладателем.',

        // message showed when the remove action has been requested on import with a status different than "completed" or "error"
        'destroy_forbidden_status' => 'Невозможно удалить импорты, находящиеся в режиме ожидания или загрузки.',

        // General error when something not-expected happen
        'destroy_error' => 'Невозможно удалить импорт. При повторном возникновении ошибки, пожалуйста, сообщите ее службе поддержки: ":error"',
        'destroy_error_dialog_title' => 'Невозможно удалить импорт.',
    ],

    'retry' => [
        'retry_btn' => 'Повторить',
        'retry_btn_hint' => 'Повторите импортирование',
        'retrying' => 'Повторное добавление ":import"...', // the import can only be added back to the queue of the imports
        'retrying_alt' => 'Повторное выполнение...',
        'retry_completed_message' => '":import" был вновь добавлен в список импортируемых.',

        // message showed when a user wants to retry an import created by another user
        'retry_forbidden_user' => 'Невозможно повторить импортирование ":import", так как вы не являетесь его обладателем.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'retry_forbidden_user_alternate' => 'Невозможно повторить импортирование, так как вы не являетесь его обладателем.',

        'retry_error_file_not_found' => 'Алгачкы маалыматтар жок кылынды. Бул импорттоп кайталап мүмкүн эмес.',

        'retry_forbidden_status' => 'Невозможно повторить импортирования, необозначеные ошибкой.',

        // General error when something not-expected happen
        'retry_error' => 'Невозможно повторить импортирование. При повторном возникновении ошибки, пожалуйста, сообщите ее службе поддержки: ":error"',
        'retry_error_dialog_title' => 'Бул кайталап мүмкүн эмес',
    ],

];
