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

    'page_title' => 'Башкаруу',

    'menu' => [

        'accounts'=>'Колдонуучулар',
        'language'=>'Тил',
        'storage'=>'Сактагыч',
        'network'=>'Тармак',
        'mail'=>'Почта',
        'update'=>'Жаңыртуу жана калыбына келтирүү',
        'maintenance'=>'Тейлөө',
        'institutions'=>'Уюмдар',
        'settings'=>'Орнотуу',
         'identity' => 'Реквизиттер',
         'licenses' => 'Лицензиялоо',

    ],

    'accounts' => [

        'disable_confirm' => ':name колдонуучуну өчүргөнү жатасыз',

        'create_user_btn' => 'Түзүү',

        'table' => [

            'name_column' => 'Аты',
            'email_column' => 'Электрондук дареги',
            'institution_column' => 'Уюм',

        ],

        'edit_account_title' => ':name өзгөртүү',

        'labels' => [

            'email' => 'Почта',
            'username' => 'Логин',
            'perms' => 'Укуктар',

            'cancel' => 'Жокко чыгаруу',

            'create' => 'Түзүү',
            'update' => 'Жаңыртуу',

            'institution' => 'Уюм',
            'select_institution' => 'Колдонуучу таандык болгон уюмду тандоо',

        ],

        'capabilities' => [

            'manage_dms' => 'K-Box башкаруу бетин көрүү',
            'change_document_visibility' => 'Документтерди жарыялоо',
            'edit_document' => 'Документ жөнүндө маалыматты өзгөртүү',
            'delete_document' => 'Документтерди өчүрүү',
            'upload_documents' => 'Документтерди жүктөө',
            'make_search' => 'Ачык долбоорлордун ичиндеги документтерди көрүү',
            'manage_own_groups' => 'Жеке коллекция менен иштөө',
            'manage_institution_groups' => 'Ачык долбоорлордун ичиндеги группаларды башкаруу',
            'manage_project_collections' => 'Ачык долбоорлордун түзүлүшүнө өзгөртүү киргизүү',
            'manage_share' => 'Башка колдонуучулардын жабык документтерин көрүү',
            'receive_share' => 'Бөлүшкөн документтерди көрүү',
            'manage_share_personal' => 'Жеке документтерди башка колдонуучулар менен бөлүшүү',
            'manage_share_private' => 'Уюм дэңгээлинде түзүлгөн колдонуучу группалары менен документ бөлүшүү',
            'clean_trash' => 'Жүктөлгөн документтерди кайтарылбас өчүрүү',

        ],

        'types' => [

            'guest' => 'Конок',
            'partner' => 'Өнөктөш',
            'content_manager' => 'Контент менеджер',
            'quality_content_manager' => 'Сапат боюнча контент менеджер',
            'project_admin' => 'Долбоор администратору',
            'admin' => 'K-Box администратору',
            'klinker' => 'K-Link колдонуучу',

        ],

        'create' => [

            'title' => 'Жаңы колдонуучуну каттоо',
            'slug' => 'Түзүү',

        ],

        'created_msg' => 'Жаңы колдонуучуну катталды. Сырсөз колдонуучунун жеке почтасына жөнөтүлдү.',
        'edit_disabled_msg' => 'Өзүңүздүн укутарыңызды өзгөртө албайсыз. Жеке маалыматыңызды <a href=":profile_url">бул жерден</a> өзгөртүңуз.',
        'disabled_msg' => 'Колдонуучу :name өчүрүлдү',
        'enabled_msg' => 'Колдонуучу :name кайра жандырылды',
        'updated_msg' => 'Колдонуучу жаңыланды',
        'mail_subject' => 'Сиздин аккаунтуңуз даяр',
        'reset_sent' => 'Сырсөздү жаңыртуу боюнача кат :name (:email) почтага жөнөтүлдү',
        'reset_not_sent' => 'Сырсөздү жаңыртуу боюнача катты бул почтага :email. :error жөнөтүү мүмкүн эмес',
        'reset_not_sent_invalid_user' => 'Колдонуучуну :email табуу мүмкүн эмес',
        'send_reset_password_btn' => 'Сырсөздү жаңыртуу',
        'send_reset_password_hint' => 'Сырсөздү өзгөртүү үчүн шилтемени жөнөтүү',
        'send_message_btn' => 'Билдирүү жөнөтүү',
        'send_message_btn_hint' => 'Колдонуучуларга билдирүү жөнөтүү',
    ],

    'language' => [

        'list_label' => 'Бул жерде болгон тилдер көрсөтүлгөн',
        'code_column' => 'Код',
        'name_column' => 'Аталышы',

    ],

    'storage' => [

        'disk_status_title' => 'Диск статусу',
        'documents_report_title' => 'Документтердин түрү',
        'disk_number' => 'Диск :number',
        'disk_type_all' => 'Башкы диск жана документтердин диски',
        'disk_type_main' => 'Башкы диск',
        'disk_type_docs' => 'Документтердин диски',
        'disk_space' => ':free <strong>бош</strong>, :used колдонулган of :total баарынан.',

        'reindexall_btn' => 'Бардык документтерди жаңыдан индексациялоо',

        'reindexing_status' => ':number документ жаңыдан индексациялоо болуп жатат...',
        'reindexing_all_status' => 'Бардык документтер жаңыдан индексациялоо болуп жатат...',
        'reindexing_status_completed' => 'Документтериңиз жаңыдан индексациялоо болуп бүттү',

        'naming_policy_title' => 'Документтерди аталыш эрежеси',
        'naming_policy_description' => 'Документтерди аталыш эрежесине дал келбеген документтер жүктөлбөйт',

        'naming_policy_btn_activate' => 'Жандыруу',
        'naming_policy_btn_save' => 'Жаңылантуу',
        'naming_policy_btn_deactivate' => 'Өчүрүү',

        'naming_policy_msg_activated' => 'Документтердин аталышынын эрежеси иштетилди',
        'naming_policy_msg_deactivated' => 'Документтердин аталышынын эрежеси өчүрүлдү',

        'all_files' => 'Баардык файлдар',

    ],

    'network' => [

        'klink_net_title' => 'K-Link сетевое соединение',
        'ksearch' => 'K-Search менен байланышуу',
        'ksearch_description' => 'Бул жерде K-Box менен K-Search байланыш абалы көрсөтүлгөн',

        'network' => '":network" менен байланышуу',
        'network_description' => 'Бул жерде K-Box менен жеткиликтүү тармактардын байланыш абалы көрсөтүлгөн',

        'net_cards_title' => 'Тармактын интерфейси',

        'no_cards' => 'Тармак менен байланыш табылган жок',

        'cards_problem' => 'Тармак картадагы маалыматты окуу мүмкүн эмес. Толук маалымат бул жерде.',

        'current_ip' => 'Учурдагы IP адрес :ip',

        'klink_status' => [
            'success' => 'Орнотулду жана текшерилди',
            'failed' => 'K-Link менен байланыш жок учурда',
        ]

    ],
    'mail' => [
        'save_btn' => 'Сактоо',
        'configuration_saved_msg' => 'Почта орнотуулары сакталды',
        'test_success_msg' => 'Ушул адрестен (:from) тест билдирүү жөнөтүлдү, почтаңызды текшерип көрүңүз ',
        'test_failure_msg' => 'Тест билдирүүнү жөнөткөнгө мүмкүн эмес',
        'enable_chk' => 'Кат жөнөтүүгө уруксат берүү',
        'enabled' => 'K-Box аркылуу билдирүү жөнөтүү мүмкүн',
        'enabled_by_configuration' => 'Билдирүү жөнөтүү үчүн орнотуулар жандырылды',
        'disabled' => 'K-Box аркылуу билдирүү жөнөтүү мүмкүн эмес',
        'test_btn' => 'Тест билдирүү жөнөтүү',
        'from_label' => 'K-Box электрондук дареги',
        'from_description' => 'Бул жерде каттар үчүн колдонгон даректи көрсөтсө болот',
         'server_configuration_label' => 'Орнотуулар',
        'server_configuration_description' => 'Бул жерде K-Box менен почта серверин бириктирүү боюнча орнотуулар көрсөтүлгөн',
        'from_name' => 'Аты',
        'from_address' => 'Почта адреси',
        'from_name_placeholder' => 'Имя (мисалы, Джон)',
        'from_address_placeholder' => 'Почта (мисалы, john@klink.org)',
        'host_label' => 'SMTP хостун адреси',
        'port_label' => 'SMTP хостун порту',
        'encryption_label' => 'Почта серверинде TLS Encryption шифрлөө протоколу болушу милдетүү',
        'username_label' => 'SMTP Server колдонуучусу',
        'password_label' => 'SMTP Server паролу',
        'log_driver_used' => 'Логинг колдонулуп жатат.Сервер орнотууларын өзгөртүүгө мүмкүн эмес.', //"log driving" to be checked. I'm too tired of translations to do it today
        'log_driver_go_to_log' => 'Почта адрестери K-Boxтун лог файлында сакталып калат. Көрүү үчүн<a href=":link">Администрация > Обслуживание и События</a> ачыңыз.',

    ],
    'update' => [],
    'maintenance' => [

        'queue_runner' => 'Асинхрондук тапшыпмаларды аткаруу үчүн сервер',

        'queue_runner_started' => 'Иштеп жатат',
        'queue_runner_stopped' => 'Жүргүзүлгөн жок',

        'queue_runner_not_running_description' => 'Тапшырма аткаруу сервери жүргүзүлгөн жок. Документтер жана каттар индексациялоосу ылайыксыз жүрүшү мүмкүн.',

        'logs_widget_title' => 'Акыркы log жазуулары',
    ],

    // Institution pages in the administration area
    'institutions' => [

        'edit_title' => ':name аталышынын өзгөртүү',
        'create_title' => 'Түзүү',
        'create_institutions_btn' => 'Түзүү',
        'saved' => 'Уюм :name сакталды',
        'update_error' => 'Уюм жөнүндө маалымат сакталган жок :error',
        'create_error' => 'Уюмду түзүүгө мүмкүн эмес: :error',
        'delete_not_possible' => 'Уюмду :name жок кылууга мүмкүн эмес',
        'delete_error' => 'Уюмду :name жок кылууга мүмкүн эмес: :error',
        'deleted' => 'Уюм :name өчүрүлдү',
        'delete_confirm' => ':name уюм тармактан чыгарылсынбы?',
        'deprecated' => 'Уюмдарды башкаруу жакын арада өзгөрүлөт. Ошондуктан, уюмдарды кошуу, өзгөртүү жана алып салуу мүмкүнчүлүктөрү чектелген.',

        'labels' => [
            'klink_id' => 'Идентификатор (K-Link тармагында)',
            'name' => 'Аталышы',
            'email' => 'Электрондук почта',
            'phone' => 'Телефон номуру',
            'url' => 'Веб-сайт',
            'thumbnail_url' => 'Сүрөттүн URL',
            'address_street' => 'Адрес',
            'address_country' => 'Мамлекет',
            'address_locality' => 'Шаар',
            'address_zip' => 'Почта индекси',
            'update' => 'Сактоо',
            'create' => 'Түзүү'
        ],
    ],

    'settings' => [
        'viewing_section' => 'Кароо',
        'viewing_section_help' => 'Сиз документтин кароо түрүн тандасыңыз болот',
        'save_btn' => 'Сактоо',
        'saved' => 'Орнотуулар жаңыланды',
        'save_error' => 'Орнотууларды сактоого мүмкүн эмес :error',

        'map_visualization_chk' => 'Картаны жандыруу',

        // Settings to enable/disable K-Link Public integration
        // 'klinkpublic_section' => 'Открытая сеть K-Link ',
        // 'klinkpublic_section_help' => 'Настроить доступ к Открытой сети K-Link',
        // 'klinkpublic_enabled' => 'Разрешить публикацию документов',
        // 'klinkpublic_debug_enabled' => 'Разрешить отладку соединения K-Link',
        // 'klinkpublic_username' => 'Пользователь, используемый для опознования в Открытой сети K-Link ',
        // 'klinkpublic_password' => 'Пароль, используемый для опознования в Открытой сети K-Link ',
        // 'klinkpublic_url' => 'URL, используемый для исходного узла Открытой сети K-Link ',

        // Settings to enable/disable UserVoice ticket integration
        'support_section' => 'Колдоо кызматы',
        'support_section_help' => 'K-Link колдоо кызматын сатып алгын болсоңуз, аутентификациялоо маркерин жазыңыз, колдонуучулар суроо-талап кылып жана жардам алууга мүмкүнчүлүгү пайда болот.',
        'support_token_field' => 'Маркер',
        'support_save_btn' => 'Сактоо',

        'analytics_section' => 'Аналитика',
        'analytics_section_help' => 'K-Link аналитикасына мүмкүндүк алуу үчүн аутентификациялоо маркерин жазыңыз',
        'analytics_token_field' => 'Токен',
        'analytics_save_btn' => 'Сактоо',

    ],
        'identity' => [
        'page_title' => 'Реквизиттер',
        'description' => 'Бул маалымат "Байланыш" баракчасында көрсөтүлөт',
        'not_complete' => 'Маалымат толук эмес',
        'suggestion_based_on_institution_hint' => 'K-Link уюмдары жөнүндө маалыматтын негизинде толтурулду',

        'contact_info_updated' => 'Сакталды',
        'update_error' => 'Маалымат өзгөртүүсүз калды :error',
    ],

    'documentlicenses' => [

        'no_licenses' => 'Лицензиялар жок',
        'view_license' => 'Лицензияны көрүү',
        'default_configuration_notice' => 'Автордук укукту коргоо боюнча орнотуулар "Бардык укуктар корголгон" деп сакталды. Эффективдүү кызматташууга жеңилирээк лицензия ылайыктуу болушу мүмкүн.',

        'default' => [
            'title' => 'Стандарттык лицензия',
            'description' => 'Жаңы жүктөлгөн файлдарга колдоно турган лицензияны тандаңыз.',
            'label' => '',
            'save' => 'Стандарттык лицензияны сактоо',
            'no_licenses_error' => 'Бул K-Box лицензиялар орнотулган жок. Сураныч, стандарттык лицензияны тандоодон алдын лицензияларды тууралаңыз.',
            'saved' => 'Стандарттык лицензия сакталды. Жаңы файлдарга карата автоматтык түрдө ":title" лицензиясы колдонулат. Аны ар бир колдонуучу өз каалоосу менен өзгөртө алат.',
            'select' => 'Лицензияны тандоо',
            'apply_default_license_to_previous' => 'Стандарттык лицензияны :count документ үчүн колдонуу',
            'apply_default_license_all' => 'Болгон документтерге стандарттык лицензияны колдонуу',

        ],
        'available' => [
            'title' => 'Лицензиялар',
            'description' => 'Колдонуучулар иштете ала турган лицензияларды тандаңыз',
            'label' => '',
            'save' => 'Сактоо',
            'no_licenses_error' => 'Лицензиялар мүмкүн эмес, орнотууларды текшериңиз',
            'saved' => 'Сакталды',
        ],
    ],

];
