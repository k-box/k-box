<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Projects related Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'page_title' => 'Проекты',
    'page_title_with_name' => 'Проект :name',

    'all_projects' => 'Все проекты',

    'new_button' => 'Новый проект',
    
    'create_page_title' => 'Создать новый проект',
    'edit_page_title' => 'Изменить :name',
    
    'edit_button' => 'Изменить',
    'delete_button' => 'Удалить',
    'close_edit_button' => 'Выйти',

    'labels' => [
        'name' => 'Название',
        'description' => 'Описание',
        'project_details' => 'Детали Проекта',
        
        'users' => 'Пользователи',
        'search_member_placeholder' => 'Поиск участника проекта...',
        'search_member_not_found' => 'Участник с таким именем или организацией не найден.',
        'add_users' => 'Добавить пользователей',
        'add_users_button' => 'Добавить',
        'users_placeholder' => 'Выбрать',
        'users_hint' => 'Начните печатать или выберите пользователя из выпадающего меню',
        
        
        'create_submit' => 'Создать',
        'edit_submit' => 'Сохранить',
        'cancel' => 'Отменить',

        'users_in_project' => 'Добавленных пользователей (:count)',
        
        'managed_by' => 'Администратор',
        'created_on' => 'Создан',
        'user_count_label' => 'Участники',
        'user_count' => ':count участник|:count участника|:count участников',
        'documents_count_label' => 'Документы',
        'documents_count' => ':count документ|:count документа|:count докуметов',
        'avatar' => 'Аватар проекта',
        'avatar_description' => 'Максимальный размер файла 200KB. Наилучший вид при 300 x 160 пикселях.',
        'avatar_remove_btn' => 'Удалить',
        'avatar_remove_confirmation' => 'Аватар проекта будет удален. Вы уверены?',
        'avatar_remove_error_generic' => 'Аватар не может быть удален.',
    ],

    'remove_user_hint' => 'Снять пользователя с Проекта',

    'removing_wait_title' => 'Снятие...',
    'removing_wait_text' => 'Снимаю пользователя...',

    'no_user_available' => 'Невозможно добавить пользователей. Скорее всего вы добавили всех в списке K-Box.',
    
    'no_members' => 'Нет пользователей. Начните добавлять.',
    
    'empty_selection' => 'Выберите проект для просмотра описания',
    'empty_projects' => 'Нет проектов. <a href=":url">Создать</a> новый проект',
    
    'errors' => [

        'exception' => 'Невозможно создать проект. (:exception)',
        
        'prevent_edit_description' => 'Изменение проекта возможно в <a href=":link">Проекты > Изменить :name</a>',
        
        'prevent_delete_description' => 'Эта проектная коллекция не может быть удалена.'
    ],
    
    'project_created' => 'Проект :name создан',
    
    'project_updated' => 'Проект :name обновлен',
    
    'no_projects' => 'В настоящее время нет проектов.',
    
    'show_documents' => 'Показать документы',

];
