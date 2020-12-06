<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Языковые ресурсы для проверки значений
    |--------------------------------------------------------------------------
    |
    | Последующие языковые строки содержат сообщения по-умолчанию, используемые
    | классом, проверяющим значения (валидатором).Некоторые из правил имеют
    | несколько версий, например, size. Вы можете поменять их на любые
    | другие, которые лучше подходят для вашего приложения.
    |
    */

    "accepted"             => "Вы должны принять :attribute.",
    "active_url"           => ":attribute не является действительным URL адресом.",
    "after"                => "Поле :attribute требует дату позднее :date.",
    'after_or_equal'       => 'Поле :attribute требует дату не ранее :date.',
    "alpha"                => "Поле :attribute может содержать только буквы.",
    "alpha_dash"           => "Поле :attribute может содержать только буквы, цифры и тире.",
    "alpha_num"            => "Поле :attribute может содержать только буквы и цифры.",
    "array"                => "Поле :attribute должно быть массивом.",
    "before"               => "В поле :attribute должна быть дата до :date.",
    'before_or_equal'      => 'Поле :attribute требует дату не позднее :date.',
    "between"              => [
        "numeric" => "Поле :attribute должно быть между :min и :max.",
        "file"    => "Размер файла в поле :attribute должен быть между :min и :max Килобайт(а).",
        "string"  => "Количество символов в поле :attribute должно быть между :min и :max.",
        "array"   => "Количество элементов в поле :attribute должно быть между :min и :max."
    ],
    "boolean"              => "Поле :attribute должно иметь значение логического типа.", // калька 'истина' или 'ложь' звучала бы слишком неестественно
    "confirmed"            => "Поле :attribute не совпадает с подтверждением.",
    "date"                 => "Поле :attribute не является датой.",
    'date_equals'          => 'Атрибут :attribute должен быть датой, равной :date.',
    "date_format"          => "Поле :attribute не соответствует формату :format.",
    "different"            => "Поля :attribute и :other должны различаться.",
    "digits"               => "Длина цифрового поля :attribute должна быть :digits.",
    "digits_between"       => "Длина цифрового поля :attribute должна быть между :min и :max.",
    'dimensions'           => ':attribute имеет недопустимые размеры изображения.',
    'distinct' => ':attribute поле имеет повторяющееся значение.',
    "email"                => ":attribute должна быть действительной",
    'ends_with' => ':attribute  должен заканчиваться одним из следующих символов: :values.',
    "exists"               => "Выбранное значение для :attribute некорректно.",
    'file' => ':attribute должен быть файлом.',
    "filled"               => "Поле :attribute обязательно для заполнения.",
    'gt' => [
        'numeric' => ':attribute должен быть больше чем :value.',
        'file' => ':attribute должен быть больше чем :value kilobytes.',
        'string' => ':attribute должен быть больше чем :value characters.',
        'array' => ':attribute должен быть больше чем :value items.',
    ],
    'gte' => [
        'numeric' => ':attribute должен быть больше или равно :value.',
        'file' => ':attribute должен быть больше или равно :value килобайтов.',
        'string' => ':attribute должен быть больше или равно :value символов.',
        'array' => ':attribute должен имет :value пунктов или больше.',
    ],    
    'gte' => [
        'numeric' => ':attribute должен быть больше или равно :value.',
        'file' => 'The :attribute должен быть больше или равно :value килобайтов.',
        'string' => 'The :attribute должен быть больше или равно :value символов.',
        'array' => 'The :attribute должен имет :value пунктов или больше.',
    ],

    "image"                => ":attribute должен быть изображением",
    "in"                   => "Выбранное значение для :attribute ошибочно.",
    'in_array'             => 'Поле :attribute не существует в :other.',  
    "integer"              => "Поле :attribute должен быть целым числом.",
    "ip"                   => "Поле :attribute должен быть действительным IP-адресом.",
    'ipv4'                 => 'Поле :attribute должен быть действительным IPv4 адресом.',
    'ipv6'                 => 'Поле :attribute должен быть действительным IPv6 адресом.',
    'json' => ':attribute должна быть валидный JSON формат.',
    'lt' => [
        'numeric' => 'Поле :attribute должен быть меньше :value.',
        'file' => 'Поле :attribute должен быть меньше :value килобайт.',
        'string' => 'Поле :attribute должен быть меньше :value символов.',
        'array' => 'Поле :attribute должен быть меньше :value пунктов.',
    ],
    'lte' => [
        'numeric' => 'Поле :attribute должен быть меньше или равно :value.',
        'file' => 'The :attribute должен быть меньше или равно :value килобайт.',
        'string' => 'The :attribute должен быть меньше или равно :value символов.',
        'array' => 'The :attribute должен быть меньше или равно :value пунктов.',
    ],
    "max" => [
        "numeric" => "Поле :attribute не может быть более :max.",
        "file"    => ":attribute не может быть более :max Кб",
        "string"  => "Количество символов в поле :attribute не может превышать :max.",
        "array"   => "Количество элементов в поле :attribute не может превышать :max."
    ],
    "mimes"                => "Поле :attribute должен быть файлом одного из следующих типов: :values.",
    'mimetypes'            => 'Поле :attribute должен быть файлом одного из следующих типов: :values.',
    "min"                  => [
        "numeric" => "Поле :attribute должен быть не менее :min.",
        "string"  => "Поле :attribute должен содержать не менее :min.",
        "file"    => "Размер файла в поле :attribute должен быть не менее :min Килобайт(а).",
        "string"  => "Введите :attribute из :min символов",
        "array"   => "Поле :attribute должен содержать не менее :min символов."
    ],
    "not_in"               => "Выбранное значение для :attribute ошибочно.",
    'not_regex' => 'Формат поле :attribute недействительным.',
    'password' => 'Пароль неверный.',
    'present' => 'Поле :attribute должен быть представлен.',
    "numeric"              => "Поле :attribute должен быть числом.",
    "regex"                => "Поле :attribute имеет ошибочный формат.",
    "required"             => "Поле :attribute обязательно для заполнения.",
    "required_if"          => "Поле :attribute обязательно для заполнения, когда :other равно :value.",
    'required_unless' => 'Поле :attribute обязательно для заполнения за исключением случая если :other находится в :values.',
    "required_with"        => "Поле :attribute обязательно для заполнения, когда :values указано.",
    "required_with_all"    => "Поле :attribute обязательно для заполнения, когда :values указано.",
    "required_without"     => "Поле :attribute обязательно для заполнения, когда :values не указано.",
    "required_without_all" => "Поле :attribute обязательно для заполнения, когда ни одно из :values не указано.",
    "same"                 => "Значение :attribute должно совпадать с :other.",
    "size"                 => [
        "numeric" => "Поле :attribute должно быть равным :size.",
        "file"    => "Размер файла в поле :attribute должен быть равен :size Килобайт(а).",
        "string"  => "Количество символов в поле :attribute должно быть равным :size.",
        "array"   => "Количество элементов в поле :attribute должно быть равным :size."
    ],
    'starts_with' => 'Поле :attribute должен начинаться с одним из следующих значений: :values.',
    'string'               => 'Поле :attribute должно быть строкой',
    "timezone"             => "Поле :attribute должно быть действительным часовым поясом.",
    'uploaded'             => 'Загрузка поля :attribute прошла безуспешно.',
    "unique"               => "Такое значение поля :attribute уже существует.",
    "url"                  => "Поле :attribute имеет ошибочный формат.",
    'uuid' => 'Поле :attribute должен содержать валидный UUID.',
    "not_array"             => "Поле :attribute должно иметь несколько значений.",
    'ensure_contains_at_least' => 'Поле :attribute должен содержать хотя бы :required.',  
    /*
    |--------------------------------------------------------------------------
    | Собственные языковые ресурсы для проверки значений
    |--------------------------------------------------------------------------
    |
    | Здесь Вы можете указать собственные сообщения для атрибутов.
    | Это позволяет легко указать свое сообщение для заданного правила атрибута.
    |
    | http://laravel.com/docs/validation#custom-error-messages
    | Пример использования
    |
    |   'custom' => [
    |       'email' => [
    |           'required' => 'Нам необходимо знать Ваш электронный адрес!',
    |       ],
    |   ],
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'Специальное сообщение',
        ],
        'capabilities' => [
            'required' => 'Отметьте хотя бы одно разрешение',
            'ensure_contains_at_least' => 'Требуемое Партнерское разрешение не установлено.',
        ],
        'users' => [
            'required' => 'Пожалуйста, выберите хотя бы одного пользователя.',
        ],
        'document' => [
            // 'required' => 'Загружаемый Вами документ превышает максимально допустимый размер '.\KBox\Upload::maximumAsKB().'KB',
            'required_alt' => 'Загружаемый Вами документ превышает максимально допустимый размер :size :unit',
        ],

        'logo' => [
            'url' => 'Введите URL изображения корректно',
            'regex' => 'Логотип должен состоять из допустимого URL файла.',
        ],
        'hero_image' => [
            'url' => 'Полноэкранное изображение должно иметь допустимое URL файла.',
            'regex' => 'Полноэкранное изображение должно иметь допустимое URL файла.',
        ],
        
         'with_users' => [
            'required' => 'Выберите хотя бы одного пользователя',
        ],
        'copyright_owner_website' => [
            'required_without' => 'Пожалуйста, укажите контактные данные владельца авторских прав, веб-сайт или/и электронную почту',
        ],
        'copyright_owner_name' => [
            'required' => 'Пожалуйста, укажите контактные данные владельца авторских прав',
        ],
        'email' => [
        	'required' => 'Введите электронную почту',
            'email' => 'Введите действительную электронную почту',
            'unique' => 'Такая электронная почта уже существует',
        ],
        'name' => [
            'required' => 'Введите имя'
        ],
        'password' => [
            'required' => 'Введите пароль',
            'confirmed' => 'Пароли не совпадают',
        ],
        'website' => [
            'url' => 'Введите адрес корректно',
        ],
        'image' => [
            'url' => 'Введите корректный адрес',
        ],
        'available_licenses' => [
        	'required' => 'Выберите хотя бы одну лицензию',
        ],
        'public_core_url' => [
            'url' => 'Введите URL корректно',
        ],
        'streaming_service_url' => [
            'url' => 'Введите корректный URL',
        ],
        'copyright_owner_email' => [
            'email' => 'Введите действительную электронную почту автора',
        ],
        'copyright_owner_website' => [
            'url' => 'Введите адрес веб-сайта корректно',
        ],
        'geoserver_url' => [
            'url' => 'Введите URL сервера корректно',
        ],
        'organization_website' => [
            'url' => 'Введите сайт организации корректно',
        ],
        'password_confirm' => [
            'same' => 'Пароли не совпадают',
        ],
        'title' => [
            'required' => 'Введите название документа'
        ],
        'send_password' => [
            'accepted' => 'Если вы не отправите автоматически сгенерированный пароль, пользователь не будет знать, какой пароль использовать.'
        ],
        'invite' => [
            'exists' => 'Срок действия используемого приглашения истек и больше не действует.',
            'required' => 'Для создания учетной записи требуется приглашение от зарегистрированного пользователя.',
        ]
     
        
    ],
    

    /*
    |--------------------------------------------------------------------------
    | Собственные названия атрибутов
    |--------------------------------------------------------------------------
    |
    | Последующие строки используются для подмены программных имен элементов
    | пользовательского интерфейса на удобочитаемые. Например, вместо имени
    | поля "email" в сообщениях будет выводиться "электронный адрес".
    |
    | Пример использования
    |
    |   'attributes' => [
    |       'email' => 'электронный адрес',
    |   )
    |
    */

    'attributes' => [
        'copyright_owner_website' => 'веб-сайт',
        'password' => 'пароль', // not capital because used in line 78 
        'email' => 'электронная почта',
        'avatar' => 'Аватар',
        'description' => 'описание'
        ],

];
