<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used primarly for the video uploader
    |
    */

    'start' => 'Начать загрузку',
    'remove' => 'Удалить из списка загрузок',
    'open_file_location' => 'Показать в коллекции',
    'cancel' => 'Отменить загрузку',
    'cancel_question' => 'Вы действительно хотите прервать загрузку?',

    'action_drop' => 'Переместите файлы сюда для начала загрузки',
    'action_select' => 'Выбрать файлы',

    'to' => 'в',

    'do_not_leave_the_page' => 'Пожалуйста, не закрывайте эту страницу до завершения процесса загрузки. Вы можете продолжать использовать ваш браузер открыв новую вкладку.',

    'upload_spec_info' => 'Разрешенный формат видео MP4 с кодеком H.264 с разрешением между 480x360 и 1920x1080 пикселей',
    
    'target' => [
    'personal' => 'в ваши <a href=":link" target="_blank" rel="noopener noreferrer">личные</a> файлы.',
    'collection' => 'в коллекцию <a href=":link" target="_blank" rel="noopener noreferrer">:name</a> в <strong>Моих коллекциях</strong>.',
    'project' => 'в проект <a href=":link" target="_blank" rel="noopener noreferrer">:name</a>.',
    'project_collection' => 'в коллекцию <a href=":link" target="_blank" rel="noopener noreferrer">:name</a> в проекте <a href=":project_link" target="_blank" rel="noopener noreferrer">:project_name</a>.',
    'error' => 'У вас нет доступа к данной коллекции',
    ],

    'status' => [
        'started' => 'Начало загрузки',
        'queued' => 'В очереди загрузок',
        'uploading' => 'Загрузка файлов',
        'completed' => 'Загрузка завершена',
        'cancelled' => 'Загрузка отменена',
        'failed' => 'Ошибка при загрузке',
    ],
];
