<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administration Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used inside the DMS Administration area
    |
    */

    'page_title' => 'Администрирование',

    'menu' => [

        'accounts'=>'Учетные записи',
        'language'=>'Язык',
        'storage'=>'Хранилище',
        'network'=>'Сеть',
        'mail'=>'Почта',
        'update'=>'Обновление и восстановление',
        'maintenance'=>'Обслуживание и События',
        'institutions'=>'Организации',
        'settings'=>'Настройки',
         'identity' => 'Реквизиты',
         'licenses' => 'Лицензии',

    ],

    'accounts' => [

        'disable_confirm' => 'Вы действительно хотите отключить :name?',

        'create_user_btn' => 'Создать',

        'table' => [

            'name_column' => 'имя',
            'email_column' => 'электронная почта',
            'institution_column' => 'организация',

        ],
        
        'edit_account_title' => 'Изменить :name',

        'labels' => [

            'email' => 'Почта',
            'username' => 'Логин',
            'perms' => 'Полномочия',

            'cancel' => 'Отменить',

            'create' => 'Создать',
            'update' => 'Обновить',

            'institution' => 'Организация',
            'select_institution' => 'Выберите организацию, к которой относится пользователь',

        ],

        'capabilities' => [

            'manage_dms' => 'Доступ к администрированию',
            'change_document_visibility' => 'Публикация документов',
            'edit_document' => 'Изменение деталей документов',
            'delete_document' => 'Перемещение документов в Корзину',
            'upload_documents' => 'Загрузка документов',
            'make_search' => 'Доступ к документам в разрешенных проектах',
            'manage_own_groups' => 'Управление личной коллекцией документов',
            'manage_institution_groups' => 'Управление группами в доступных проектах',
            'manage_project_collections' => 'Доступ к разделу Проекты', // in the past: Возможность внесения изменений в структуре доступных проектов
            'create_projects' => 'Создание проектов',
            'manage_share' => 'Возможность предоставления другим пользователям доступа к документам', // in the past: Предоставление доступа к неопубликованным документам другим пользователям
            'receive_share' => 'Доступ к разделу Совместные', // in the past: Просмотр документов в совместном доступе
            'manage_share_personal' => 'Предоставление доступа к неопубликованным документам другим пользователям',
            'clean_trash' => 'Возможность очистить Корзину',

        ],
        
        'types' => [

            'guest' => 'Гость',
            'partner' => 'Партнер',
            'content_manager' => 'Контент менеджер',
            'quality_content_manager' => 'Качественный Контент Менеджер',
            'project_admin' => 'Администратор проекта',
            'admin' => 'Администратор K-Box',
            'klinker' => 'K-Linkер',

        ],

        'create' => [

            'title' => 'Создать новую учетную запись',
            'slug' => 'Создать',

        ],

        'created_msg' => 'Пользователь создан. Пароль был отправлен лично пользователю на почту',
        'edit_disabled_msg' => 'Вы не можете изменить свои полномочия. Конфигурация учетной записи может быть совершена через <a href=":profile_url">страницу настроек</a>.',
        'disabled_msg' => 'Пользователь :name отключен',
        'enabled_msg' => 'Пользователь :name восстановлен',
        'updated_msg' => 'Пользователь обновлен',
        'mail_subject' => 'Ваша учетная запись K-Link K-Box готова',
        'reset_sent' => 'Письмо с настройками для сброса пароля было отправлено на :name (:email)',
        'reset_not_sent' => 'Письмо о сбросе пароля не может быть отправлено на :email. :error',
        'reset_not_sent_invalid_user' => 'Пользователь :email не может быть найден.',
        'send_reset_password_btn' => 'Сброс пароля',
        'send_reset_password_hint' => 'Запросить ссылку на изменение пароля пользователем',
        'send_message_btn' => 'Отправить сообщение',
        'send_message_btn_hint' => 'Отправить сообщение каждому пользователю',
    ],

    'language' => [

        'list_label' => 'Здесь указаны поддерживаемые языки',
        'code_column' => 'Код',
        'name_column' => 'Название',

    ],

    'storage' => [

        'disk_status_title' => 'Статус диска',
        'documents_report_title' => 'Типы документов',
        'disk_number' => 'Диск :number',
        'disk_type_all' => 'Главный диск и диск документов',
        'disk_type_main' => 'Главный диск',
        'disk_type_docs' => 'Диск документов',
        'disk_space' => ':free <strong>свободно</strong>, :used использовано of :total всего.',

        'reindexall_btn' => 'Переиндексировать все документы',

        'reindexing_status' => 'Переиндексация :number документов...',
        'reindexing_all_status' => 'Переиндексация всех документов...',
        'reindexing_status_completed' => 'Ваши документы были переиндексированы.',

        'naming_policy_title' => 'Правила наименования документов',
        'naming_policy_description' => 'Система может запретить загрузку файлов, не отвечающих данным правилам наименования документов',

        'naming_policy_btn_activate' => 'Включить',
        'naming_policy_btn_save' => 'Обновить',
        'naming_policy_btn_deactivate' => 'Отключить',

        'naming_policy_msg_activated' => 'Правила наименования включены',
        'naming_policy_msg_deactivated' => 'Правила наименования выключены',

        'all_files' => 'Все файлы',

    ],

    'network' => [

        'klink_net_title' => 'K-Link сетевое соединение',
        'ksearch' => 'Соединение с поисковой машиной K-Search',
        'ksearch_description' => 'Здесь показан статус соединения K-Box с поисковой машиной.',
        
        'network' => 'Соединение с ":network"',
        'network_description' => 'Здесь показан статус соединения K-Box доступными сетями.',

        'net_cards_title' => 'Сетевой интерфейс',

        'no_cards' => 'Сетевое соединение не обнаружено.',

        'cards_problem' => 'Проблема с чтением информации с сетевой карты. Здесь подробный ответ разработчика',

        'current_ip' => 'Текущий IP адрес :ip',

        'klink_status' => [
            'success' => 'Установлено и верифицировано',
            'failed' => 'Не может соединиться с K-Link Корнем',
        ]

    ],
    'mail' => [
        'save_btn' => 'Сохранить',
        'configuration_saved_msg' => 'Настройки почты были успешно сохранены.',
        'test_success_msg' => 'Тестовое сообщение было успешно отправлено (от :from). Проверьте входящие письма.',
        'test_failure_msg' => 'Тестовое сообщение не может быть отправлено в связи с ошибкой.',
        'enable_chk' => 'Разрешить отправку писем',
        'enabled' => 'Отправка сообщений с K-Box возможна',
        'enabled_by_configuration' => 'Настройки для отправки сообщений были включены',
        'disabled' => 'Невозможно отправить сообщение с K-Box',
        'test_btn' => 'Отправить тестовое сообщение',
        'from_label' => 'Электронный адрес K-Box',
        'from_description' => 'Здесь вы можете указать имя и адрес, используемые для всех отправленных сообщений.',
         'server_configuration_label' => 'Настройки',
        'server_configuration_description' => 'Здесь указаны настройки для присоединения K-Box к почтовому серверу',
        'from_name' => 'Имя',
        'from_address' => 'Почтовый адрес',
        'from_name_placeholder' => 'Имя (например, Джон)',
        'from_address_placeholder' => 'Почта (например, john@klink.org)',
        'host_label' => 'SMTP адрес хоста',
        'port_label' => 'SMTP порт хоста',
        'encryption_label' => 'Протокол шифрования почтового адреса',
        'username_label' => 'Пользователь SMTP Server ',
        'password_label' => 'Пароль SMTP Server ',
        'log_driver_used' => 'Используется логинг. Вы не можете изменить настройки сервера.', //"log driving" to be checked. I'm too tired of translations to do it today 
        'log_driver_go_to_log' => 'Почтовые адреса будут сохранены в лог файле K-Box. Для просмотра, пройдите в <a href=":link">Администрация > Обслуживание и События</a>.',
   
    ],
    'update' => [],
    'maintenance' => [

        'queue_runner' => 'Сервер выполнения асинхронных задач',

        'queue_runner_started' => 'Запущено и слушает',
        'queue_runner_stopped' => 'Не запускается',

        'queue_runner_not_running_description' => 'Сервер выполнения задач не запускается - индексация Документов и Почтовых Сообщений может не совершаться надлежащим образом.',
        
        'logs_widget_title' => 'Последний ввод лога',
    ],
    
    // Institution pages in the administration area
    'institutions' => [
        
        'edit_title' => 'Изменить детали :name',
        'create_title' => 'Создать',
        'create_institutions_btn' => 'Добавить',
        'saved' => 'Организация :name обновлена.',
        'update_error' => 'Детали не были сохранены: :error',
        'create_error' => 'Организация не может быть создана: :error',
        'delete_not_possible' => 'В настоящее время организация :name используется для документов и/или пользовательской принадлежности. Перед удалением, пожалуйста, переместите документы и/или пользовательскую принадлежность.',
        'delete_error' => 'Организация :name не может быть удалена: :error',
        'deleted' => 'Организация :name была удалена.',
        'delete_confirm' => 'Удалить организацию :name из сети?',
        'deprecated' => 'Управление организациями будет изменено. Для подготовки вашего K-Boxа к поддержке последующих функциональностей, добавление, изменение и удаление организаций недоступно.',
                
        'labels' => [
            'klink_id' => 'Идентификатор (в Сети K-Link)',
            'name' => 'Название',
            'email' => 'Электронная почта для получения информации',
            'phone' => 'Телефонный номер секретаря',
            'url' => 'Адрес вебсайта',
            'thumbnail_url' => 'URL картинки',
            'address_street' => 'Адрес',
            'address_country' => 'Страна ',
            'address_locality' => 'Город ',
            'address_zip' => 'Почтовый Индекс',
            'update' => 'Сохранить',
            'create' => 'Создать'
        ],
    ],
    
    'settings' => [
        'viewing_section' => 'Просмотр',
        'viewing_section_help' => 'Вы можете настроить тип просмотра документов для пользователя.',
        'save_btn' => 'Сохранить',
        'saved' => 'Настройки были обновлены. Пользователь может увидеть обновления, перезагрузив страницу.',
        'save_error' => 'Данные настройки не могут быть сохранены. :error',
        
        'map_visualization_chk' => 'Включить Карту',
        
        // Settings to enable/disable K-Link Public integration
        // 'klinkpublic_section' => 'Открытая сеть K-Link ',
        // 'klinkpublic_section_help' => 'Настроить доступ к Открытой сети K-Link',
        // 'klinkpublic_enabled' => 'Разрешить публикацию документов',
        // 'klinkpublic_debug_enabled' => 'Разрешить отладку соединения K-Link',
        // 'klinkpublic_username' => 'Пользователь, используемый для опознования в Открытой сети K-Link ',
        // 'klinkpublic_password' => 'Пароль, используемый для опознования в Открытой сети K-Link ',
        // 'klinkpublic_url' => 'URL, используемый для исходного узла Открытой сети K-Link ',
        
        // Settings to enable/disable UserVoice ticket integration
        'support_section' => 'Поддержка',
        'support_section_help' => 'Если у Вас есть подписка на поддержку команды разработчиков K-Link, укажите маркер аутентификации для обеспечения ваших пользователей возможностью отправки запросов и получения помощи.',
        'support_token_field' => 'Маркер',
        'support_save_btn' => 'Сохранить',
        
        'analytics_section' => 'Аналитика',
        'analytics_section_help' => 'Аналитика предоставляет возможность исследования взаимодействий пользователей с системой. Укажите маркер аутентификации для подключения к аналитике K-Link.',
        'analytics_token_field' => 'Токен',
        'analytics_save_btn' => 'Сохранить',
        
    ],    
        'identity' => [
        'page_title' => 'Реквизиты',
        'description' => 'Данная информация о вашей организации будет отображаться на странице "Контакты".',
        'not_complete' => 'Неполная информация.',
        'suggestion_based_on_institution_hint' => 'Сведения были добавлены на основе доступной информации об организациях K-Link.',

        'contact_info_updated' => 'Сохранено',
        'update_error' => 'Сведения не изменены. :error',
    ],

    'documentlicenses' => [

        'no_licenses' => 'Нет доступных лицензий',
        'view_license' => 'Просмотреть лицензию',
        'default_configuration_notice' => 'Стандартные настройки по защите авторского права были заданы как "Все права защищены". Примите во внимание, что более свободные лицензии могут способствовать более эффективному сотрудничеству.',
        
        'default' => [
            'title' => 'Стандартная лицензия',
            'description' => 'Стандартная лицензия  применяется по отношению к новым загрузкам',
            'label' => '',
            'save' => 'Сохранить',
            'no_licenses_error' => 'Лицензии не настроены на данном K-Box. Пожалуйста, настройте их перед выбором стандартной лицензии.',
            'saved' => 'Стандартная лицензия ":title" сохранена и будет автоматически применяться к новым файлам. Лицензию отдельных файлов можно изменить на странице их редактирования.',
            'select' => 'Выбрать лицензию',
            'apply_default_license_to_previous' => 'Применить стандартную лицензию к :count документу без лицензии|Применить стандартную лицензию к :count документам без лицензии',
            'apply_default_license_all' => 'Применить данную лицензию к уже существующим документам',

        ],
        'available' => [
            'title' => 'Доступные лицензии на данном K-Box',
            'description' => 'Вы можете настроить лицензии, которые будут применяться к данным, загруженным на этот K-Box',
            'label' => '',
            'save' => 'Сохранить',
            'no_licenses_error' => 'Нет доступных лицензий, пожалуйста проверьте настройки',
            'saved' => 'Готово',
        ],
    ],

];

