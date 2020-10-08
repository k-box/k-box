<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collections Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'collections' => [
        'title'        => 'Коллекция',
        'personal_title' => 'Мои Коллекции',
        'shared_title' => 'Коллекции, которыми поделились со мной',
        'private_title' => 'Проекты',
        'description'   => 'Коллекции помогают упорядочить ваши документы.',
        
        'empty_private_msg' => 'На данный момент проектов нет',

    ],

    'create_btn' => 'Создать',
    'save_btn' => 'Сохранить',
    'loading' => 'Сохраняю...',

    'panel_create_title' => 'Новая коллекция',

    'panel_edit_title' => 'Изменить <strong>:name</strong>',

    'created_on' => 'создано',
    'created_by' => 'создано',

    'private_badge_label' => 'Личная коллекция документов',

    'group_icon_label' => 'Коллекция',
    'group_icon_label_personal' => 'Личная коллекция',
    'group_icon_label_project' => 'Коллекция проектов',

    'empty_msg' => 'Нет существующих коллекций. Создайте коллекцию.',

    'form' => [
        'collection_name_placeholder' => 'Введите название коллекции',
        'collection_name_label' => 'Название',

        'parent_label' => 'В личной коллекции: <strong>:parent</strong>',
        'parent_project_label' => 'В проектной коллекции: <strong>:parent</strong>',

        'make_public' => 'Сделать данную коллекцию видимой для пользователей Проекта.',
        'make_private' => 'Сделать эту коллекцию Личной',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Удалить :collection?',
        'dialog_title_alt' => 'Вы хотите удалить коллекцию?',
        'dialog_text' => 'Вы собираетесь удалить :collection. Данное действие удалит коллекцию, но не содержимые в ней документы.',
        'dialog_text_alt' => 'Вы собираетесь удалить выбранную Коллекцию. Данное действие удалит коллекцию, но не содержащиеся в ней документы.',
        
        'deleted_dialog_title' => 'Коллекция :collection удалена',
        'deleted_dialog_title_alt' => 'Удалено',
        
        'cannot_delete_dialog_title' => 'Невозможно удалить ":collection"',
        'cannot_delete_dialog_title_alt' => 'Не удалось удалить',
        
        'cannot_delete_general_error' => 'Невозможно удалить выбранные элементы. Удаление не успешно.',
        
        'forbidden_trash_personal_collection' => 'Вы не можете удалить коллекцию :collection, её создал другой пользователь',
        'forbidden_delete_shared_collection' => 'Вы не можете удалить коллекцию ":collection", ею поделился другой пользователь',
        'forbidden_delete_personal_collection' => 'Вы не можете удалить коллекцию :collection, её создал другой пользователь',
        'forbidden_delete_collection' => 'Невозможно удалить :collection. У вас отсутствуют пользовательские права на совершение данной операции.',
        'forbidden_delete_project_collection' => ':collection не может быть удалена. Данная коллекция находится в проекте, где у вас отсутствуют полномочия редактирования.',
        'forbidden_delete_project_collection_not_creator' => 'Вы не можете удалить коллекцию :collection, её создал другой пользователь',
        'forbidden_delete_project_collection_not_manager' => 'Удалить коллекцию :collection может только менеджер проекта',

    ],
    
    'move' => [
        'moved' => 'Коллекция ":collection" перемещена',
        'moved_alt' => 'Готово',
        'moved_text' => 'Коллекция была перемещена, мы обновляем вашу визуализацию...',
        'error_title' => 'Невозможно переместить :collection',
        'error_title_alt' => 'Невозможно переместить коллекцию',
        'error_text_generic' => 'Перемещение не завершено в связи с ошибкой. Пожалуйста, свяжитесь с Администратором K-Box.',
        'error_not_collection' => 'Перемещение доступно только для коллекций.',
        'error_same_collection' => 'Невозможно переместить коллекцию.',
        'move_to_title' => 'Переместить в ":collection"?',
        'move_to_project_title' => 'Переместить в ":collection"?',
        'move_to_project_title_alt' => 'Переместить в Проект?',
        'move_to_project_text' => 'Вы собираетесь переместить личную коллекцию под Проект. ":collection" и субколлекции будут доступны для всех пользователей Проекта.',
        'move_to_personal_title' => 'Сделать коллекцию личной?',
        'move_to_personal_text' => 'Вы собираетесь переместить коллекцию из Проекта в Личные. ":collection" перестанет быть доступной для пользователей Проекта.',
    
        'errors' => [
            'personal_not_all_same_user' => 'Вы не можете переместить коллекцию ":collection", другой пользователь создал внутри неё коллекцию ":collection_cause"',
            'personal_not_all_same_user_empty_cause' => 'Вы не можете переместить коллекцию ":collection", её создал другой пользователь',
            'no_project_collection_permission' => 'Вы не можете переместить коллекцию',
            'no_access_to_collection' => 'У Вас нет доступа к коллекции',
            'has_shares_to_non_members' => 'Некоторые пользователи, имеющие доступ к коллекции, не являются участниками проекта. Коллекцию нельзя переместить, пока все пользователи не будут включены в проект.',

        ],
    
    ],
    
    'access' => [
        'forbidden' => 'Доступ к ":name" невозможен из-за пользовательких полномочий.',
        'forbidden_alt' => 'У вас отсутствуют пользователькие полномочия доступа к данной коллекции',
    ],

    'add_documents' => [
        'forbidden' => 'Невозможно добавить документы в ":name" из-за отсутствия пользовательких полномочий.',
        'forbidden_alt' => 'У вас отсутствуют пользователькие полномочия добавления документов в коллекцию',
    ],

    'remove_documents' => [
        'forbidden' => 'Невозможно удалить документы из ":name" из-за отсутствия пользовательких полномочий.',
        'forbidden_alt' => 'У вас отсутствуют пользователькие полномочия удаления документов из коллекции',
    ],

];
