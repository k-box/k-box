<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Profile Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the users profile page
    |
    */

    'page_title' => ':name',

    'profile' => 'Профиль',

    'go_to_profile' => 'Просмотреть профайл',
    
    'messages' => [
        'mail_changed' => 'Адрес электронной почты был изменен.',
        'name_changed' => 'Имя пользователя было изменено.',
        'info_changed' => 'Информация о пользователе была изменена.',
        'password_changed' => 'Пароль был изменен.',
        'language_changed' => 'Язык был изменен.',
    ],

    'errors' => [
        'username_already_taken' =>  'Имя занято, попробуйте другое.',
    ],

    'labels' => [
        'nicename' => 'Имя',
        'nicename_hint' => 'Предпочитаемое имя пользователя',
        'password' => 'Пароль',
        'password_description' => 'Пароль должен состоять минимум из 8 символов и может содержать буквы, цифры и специальные символы',
        'password_confirm' => 'Подтвердите ваш пароль',
        'language' => 'Выберите предпочитаемый язык',
        'organization_name' => 'Организация',
        'organization_name_hint' => 'Название организации, к которой вы относитесь',
        'organization_website' => 'Веб-страница',
        'organization_website_hint' => 'Веб-страница организации. Например, https://your-organization.com',
    ],

    'change_password_btn' => 'Обновить',
    'update_profile_btn' => 'Обновить',
    'change_mail_btn' => 'Обновить',
    'change_language_btn' => 'Обновить',

    'info_section' => 'Информация',
    'email_section' => 'Изменить почту',
    'password_section' => 'Изменить пароль',
    'language_section' => 'Изменить язык пользовательского интерфейса',

    'starred_count_label' => '{0} нет документов в Избранном|{1} :number документ в Избранном|{2,4} :number документа в Избранном|{5,*} :number документов в Избранном',
    'documents_count_label' => '{0} загруженных документов нет| {1} :number загруженный документ|{2,4} :number загруженных документа |{5,*} :number загруженных документов',
    'collections_count_label' => '{0} коллекций нет|{1} :number коллекция|{2,4} :number коллекции |{5,*} :number коллекций',
    'shared_count_label' => '{0} прямых доступов не создано|{1} :number документ с прямым доступом|{2,4} :number документа с прямым доступом|{5,*} :number документов с прямым доступом',

    'account_settings' => 'Настройки аккаунта',

    'privacy' => [
        'privacy' => 'Конфиденциальность',
        'section_name' => 'Изменить настройки конфиденциальности',
        'section_description' => '',

        'activity' => [
            'consent_given' => 'Вы дали согласие :date',
            'consent_withdrawn_by_system' => 'Согласие было отозвано из-за изменения политики конфиденциальности :date',
            'consent_withdrawn_by_user' => 'Вы отозвали согласие :date',
        ],

    ],    'update_privacy_preferences' => 'Обновить настройки конфиденциальности',

];
