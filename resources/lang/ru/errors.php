<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Generic Errors Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used inside for rendering error messages
    |
    */

    'unknown' => 'Неизвестная ошибка в запросе',

    'upload' => [
        'simple' => 'Ошибка загрузки :description',
        'filenamepolicy' => 'Файл :filename не отвечает правилам названия документов.',
        'filealreadyexists' => 'Файл :filename уже существует.',
    ],
    
        'filealreadyexists' => [
        'generic' => 'Документ :name уже существует в K-DMS под именем <strong>":title"</strong>.',
        'incollection' => 'Документ уже доступен в <a href=":collection_link"><strong>":collection"</strong></a> под именем <strong>":title"</strong>',
        'incollection_by_you' => 'Вы уже загрузили данный документ как <strong>":title"</strong> в <a href=":collection_link"><strong>":collection"</strong></a>',
        'by_you' => 'Вы уже загрузили данный документ как <strong>":title"</strong>',
        'revision_of_document' => 'Загружаемый документ является обновленной версией <strong>":title"</strong>, который добавил(a) :user (:email)',
        'revision_of_your_document' => 'Загружаемый документ является обновленной версией Вашего документа под именем <strong>":title"</strong>',
        'by_user' => ':user (:email) уже добавил(a) данный документ в K-DMS.',
        'in_the_network' => 'Документ уже доступен в <strong>:network</strong> под именем <strong>":title"</strong>. Добавил :institution',
    ],

    'group_already_exists_exception' => [
        'only_name' => 'Коллекция с таким именем ":name" уже существует.',
        'name_and_parent' => 'Коллекция ":name" под ":parent" уже существует.',
    ],

    'generic_text' => 'Произошло что-то неожиданное.',
    'generic_text_alt' => 'Произошло что-то неожиданное. :error',
    'generic_title' => 'Ошибка',

    'reindex_all' => 'Переиндексация всех процедур не может быть завершена в связи с ошибкой. Посмотрите журнал ошибок или свяжитесь с администратором.', //logs as list of errors

    'token_mismatch_exception' => 'Время вашей сессии истекло. Пожалуйста, обновите страницу для продолжения работы. Спасибо.',

    'not_found' => 'Ресурс не найден.',
    
    'document_not_found' => 'Нужный вам документ не может быть найден или был удален.',

    'forbidden_exception' => 'Нет доступа к странице.',
    'forbidden_edit_document_exception' => 'Изменение документа невозможно.',
    'forbidden_see_document_exception' => 'Просмотр документа невозможен, так как он находится в личном пользовании.',
    
    'fatal' => 'Фатальная ошибка :reason',

    'panels' => [
        'title' => 'Непредвиденная ошибка.',
        'prevent_edit' => 'Невозможно изменить :name',
    ],

    'import' => [
        'folder_not_readable' => 'Папка :folder недоступна для чтения. Убедитесь о наличии соответствующих полномочий.',
        'url_already_exists' => 'Файл из вебсайта с тем же url (:url) уже был импортирован.',
        'download_error' => 'Документ ":url" не может быть загружен. :error',
    ],

    'group_edit_institution' => "Вы не можете изменять группы, созданные на организационном уровне.",
    'group_edit_project' => "Вы не можете редактировать коллекции Проекта.",
    'group_edit_else' => "Вы не можете вносить изменения в чужую группу.",

    '503_title' => 'Обслуживание K-Link DMS',
    '503_text' => '<strong>DMS</strong> в данный момент находится<br/><strong>на техническом обслуживании</strong><br/><small>скоро вернемся :)</small>',

    '500_title' => 'Ошибка - K-Link DMS',
    '500_text' => 'Что-то <strong>плохое</strong><br/>и неожиданное <strong>произошло</strong>,<br/>мы жутко извиняемся.',

    '404_title' => 'Не найден в K-Link DMS',
    '404_text' => '<br/>Данная <br/><strong>страница</strong> больше <strong>не существует</strong>',
    
    '401_title' => 'Вы не можете просмотреть страницу K-Link DMS',
    '401_text' => 'Скорее всего вы <strong>не можете</strong> просмотреть страницу<br/>из-за вашей <strong>авторизации</strong>.',

    '405_title' => 'Данный метод не разрешен на K-Link DMS',
    '405_text' => 'Не называй меня больше так.',
    
    '413_title' => 'Чрезмерно большой размер документа',
    '413_text' => 'Загружаемый вами файл превышает максимально допустимый размер.',

    'klink_exception_title' => 'Ошибка сервисов K-Link.',
    'klink_exception_text' => 'Ошибка в подключении к сервисам K-Link.',
    
    'page_loading_title' => 'Проблема загрузки',
    'page_loading_text' => 'Загрузка страницы кажется замедленной и некоторая функциональность может быть недоступна. Пожалуйста, обновите страницу.',

    'dragdrop' => [
        'not_permitted_title' => 'На данный момент перетаскивание недоступно',
        'not_permitted_text' => 'Вы не можете совершить перетаскивание.',
        'link_not_permitted_title' => 'Перетаскивание ссылок недоступно.',
        'link_not_permitted_text' => 'В настоящее время вы не можете перетаскивать ссылки на вебсайты.',
    ],
    
    'reindex_failed' => 'Последние изменения могут не быть отражены в поиске. Пожалуйста, свяжитесь с командой поддержки для более подробной информации.',

    'support_widget_opened_for_you' => 'Для вас доступна служба поддержки. Пожалуйста, напишите нам для исследования ошибки. Спасибо!',
    'go_back_btn' => 'Назад',
    
    'preference_not_saved_title' => 'Пользовательские настройки не сохранены',
    'preference_not_saved_text' => 'К сожалению, пользовательские настройки не были сохранены. Пожалуйста, попробуйте позже.',

    'generic_form_error' => 'У вас возникли ошибки. Пожалуйста, исправьте их для продолжения',

];
