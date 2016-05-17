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

	'menu' => array(

		'accounts'=>'Учетные записи',
		'language'=>'Язык',
		'storage'=>'Хранилище',
		'network'=>'Сеть',
		'mail'=>'Почта',
		'update'=>'Обновление и восстановление',
		'maintenance'=>'Обслуживание и События',
		'institutions'=>'Организации',
    	'settings'=>'Настройки',

	),

	'accounts' => array(

		'disable_confirm' => 'Вы действительно хотите отключить :name',

		'create_user_btn' => 'Создать пользователя',

		'table' => array(

			'name_column' => 'имя',
			'email_column' => 'электронная почта',
			'institution_column' => 'организация',

		),
		
		'edit_account_title' => 'Изменить :name',

		'labels' => array(

			'email' => 'Почта',
			'username' => 'Логин',
			'perms' => 'Разрешения',

			'cancel' => 'Отменить',

			'create' => 'Создать',
			'update' => 'Обновить',

			'institution' => 'Организация',
			'select_institution' => 'Выберите к какой организации относится пользователь...',

		),

		'capabilities' => array(

			'manage_dms' => 'Пользователь имеет доступ к управляющей панели DMS',
			'manage_dms_users' => 'Пользователь может управлять другими пользователями DMS',
			'manage_dms_log' => 'Пользователь может видеть журнал DMS',
			'manage_dms_backup' => 'Пользователь может совершить резервное копирование и восстановление DMS',
			'change_document_visibility' => 'Пользователь может изменить доступность документов',
			'edit_document' => 'Пользователь может исправлять документы',
			'delete_document' => 'Пользователь может удалять документы',
			'import_documents' => 'Пользователь может импортировать документы из папок или внешнего URL',
			'upload_documents' => 'Пользователь может загружать документы',
			'make_search' => 'Пользователь имеет доступ ко всем закрытым документам организации',
			'manage_own_groups' => 'Пользователь может управлять личной коллекцией документов',
			'manage_institution_groups' => 'Пользователь может управлять коллекцией документов организации',
			'manage_project_collections' => 'Пользователь может управлять проектными коллекциями',
			
			'manage_share' => 'Пользователь может делиться личными документами с другим пользователем или группой пользователей',
			'receive_share' => 'Пользователь может просмотреть документы в совместном доступе',
		
			'manage_share_personal' => 'Пользователь может делиться личными документами с одним пользователем или "личной" группой пользователей',
			'manage_share_private' => 'Пользователь может делиться документами с группой пользователей, определенных на организационном уровне',
			
			'clean_trash' => 'Пользователь имеет право на безвозвратное удаление документов',
			
			'manage_personal_people' => 'Пользователь может создать/изменять группы пользователей, определенных на личном уровне',
			'manage_people' => 'Пользователь может создать/изменять группы пользователей, принадлежащих организации',

		),
		
		'types' => array(

			'guest' => 'Гость',
	        'partner' => 'Партнер',
	        'content_manager' => 'Контент менеджер',
	        'quality_content_manager' => 'Качественный Контент Менеджер',
	        'project_admin' => 'Администратор проекта',
	        'admin' => 'Администратор K-Box',
	        'klinker' => 'K-LINKер',

		),

		'create' => array(

			'title' => 'Создать новую учетную запись',
			'slug' => 'Создать',

		),

		'created_msg' => 'Пользователь создан, пароль был отправлен лично пользователю на почту',
		'edit_disabled_msg' => 'Вы не можете изменить свои полномочия. Конфигурация учетной записи может быть совершена через <a href=":profile_url">страницу настроек</a>.',
		'disabled_msg' => 'Пользователь :name отключен',
		'enabled_msg' => 'Пользователь :name восстановлен',
		'updated_msg' => 'Пользователь обновлен',
		'mail_subject' => 'Ваша учетная запись K-LINK DMS готова',
		'reset_sent' => 'Письмо о сбросе пароля было отправлено на :name (:email)',
		'reset_not_sent' => 'Письмо о сбросе пароля не может быть отправлено на :email. :error',
		'reset_not_sent_invalid_user' => 'Пользователь, :email, не может быть найден.',
		'send_reset_password_btn' => 'Сброс пароля',
		'send_reset_password_hint' => 'Запросить ссылку на сброс пароля пользователем',
		'send_message_btn' => 'Отправить сообщение',
		'send_message_btn_hint' => 'Отправить сообщение каждому пользователю',
	),

	'language' => array(

		'list_label' => 'Здесь указаны поддерживаемые языки.',
		'code_column' => 'Код языка',
		'name_column' => 'Название языка',

	),

	'storage' => array(

		'disk_status_title' => 'Статус диска',
		'documents_report_title' => 'Типы документов',
		'disk_number' => 'Диск :number',
		'disk_type_all' => 'Главный и диск документов',
		'disk_type_main' => 'Главный диск',
		'disk_type_docs' => 'Диск документов',
		'disk_space' => ':free <strong>свободно</strong>, :used использовано of :total всего.',

		'reindexall_btn' => 'Переиндексировать все документы',

		'reindexing_status' => 'Переиндексация :number документов...',
		'reindexing_all_status' => 'Переиндексация всех документов...',
		'reindexing_status_completed' => 'Ваши документы были переиндексированы.',

		'naming_policy_title' => 'Правила наименований документов',
		'naming_policy_description' => 'Вы можете запретить загрузку файлов, не отвечающих правилам наименования документов',

		'naming_policy_btn_activate' => 'Включить правила наименования документов',
		'naming_policy_btn_save' => 'Обновить',
		'naming_policy_btn_deactivate' => 'Отключить правила наименования документов',

		'naming_policy_msg_activated' => 'Правила наименования включены',
		'naming_policy_msg_deactivated' => 'Правила наименования выключены',

	),

	'network' => array(

		'klink_net_title' => 'K-Link сетевое соединение',

		'net_cards_title' => 'Сетевой интерфейс',

		'no_cards' => 'Сетевое соединение не обнаружено.',

		'cards_problem' => 'Проблема с чтением информации с сетевой карты. Здесь подробный ответ разработчика', 

		'current_ip' => 'Текущий IP адрес :ip',

		'klink_status' => array(
			'success' => 'Установлено и верифициравано',
			'failed' => 'Не может соединиться с K-Link Корнем',
		)

	),
	'mail' => array(
		'save_btn' => 'Сохранить настройки почты',
		'configuration_saved_msg' => 'Настройки почты были успешно сохранены.',
		'test_success_msg' => 'Тестовое сообщение было успешно отправлено (от :from). Проверьте входящие письма.',
		'test_failure_msg' => 'Тестовое сообщение не может быть отправлено в связи с ошибкой.',
		'enable_chk' => 'Разрешить отправку писем',
		'test_btn' => 'Отправить тестовое сообщение',
		'from_label' => 'Глобальный "От" Адрес',
		'from_description' => 'Здесь, вы можете указать имя и адрес, используемые глобально для всех сообщений, отправленных K-Link DMS.',
		'from_name_placeholder' => 'Имя (например, Джон)',
		'from_address_placeholder' => 'Почта (например, john@klink.org)',
		'host_label' => 'SMTP адрес хоста',
		'port_label' => 'SMTP порт хоста',
		'encryption_label' => 'E-Mail Encryption Protocol',
		'username_label' => 'SMTP Server Пользователь',
		'password_label' => 'SMTP Server Пароль',
	),
	'update' => array(),
	'maintenance' => array(

		'queue_runner' => 'Сервер выполнения асинхронных задач',

		'queue_runner_started' => 'Запущено и слушает',
		'queue_runner_stopped' => 'Не запускается',

		'queue_runner_not_running_description' => 'Сервер выполнения задач не запускается - индексация Документов и Почтовых Сообщений может не совершаться надлежащим образом.',
		
		'logs_widget_title' => 'Последний ввод лога', 
	),
    
    // Institution pages in the administration area
    'institutions' => array(
		
		'edit_title' => 'Изменить детали :name',
		'create_title' => 'Создать новую Организацию',
		'create_institutions_btn' => 'Добавить новую Организацию',
		'saved' => 'Организация :name обновлена.',
		'update_error' => 'Детали Организации не были сохранены: :error',
		'create_error' => 'Организация не может быть создана: :error',
		'delete_not_possible' => 'В настоящее время организация :name используется для документов и/или пользовательской принадлежности. Перед удалением, пожалуйста, переместите документы и/или пользовательскую принадлежность.',
		'delete_error' => 'Организация :name не может быть удалена: :error',
		'deleted' => 'Организация :name была удалена.',
		'delete_confirm' => 'Удалить организацию :name из сети?',
		
		'labels' => array(
			'klink_id' => 'Идентификатор Организации (в Сети K-Link)', 
			'name' => 'Название Организации', 
			'email' => 'Электронная почта Организации для получения информации', 
			'phone' => 'Телефонный номер секретаря Организации', 
			'url' => 'Адрес вебсайта Организации', 
			'thumbnail_url' => 'Картинка или аватарка Организации (url картинки)', 
			'address_street' => 'Адрес Организации', 
			'address_country' => 'Страна Организации', 
			'address_locality' => 'Город Организации', 
			'address_zip' => 'Почтовый Индекс', 
			'update' => 'Сохранить Данные',
			'create' => 'Создать Организацию'
		),
	),
	
	'settings' => array(
		'viewing_section' => 'Просмотр',
		'viewing_section_help' => 'Вы можете настроить тип просмотра документов для пользователя.',
		'save_btn' => 'Сохранить настройки',
		'saved' => 'Настройки были обновлены. Пользователь может увидеть обновления после перезагрузки страницы.',
		'save_error' => 'Данные настройки не могут быть сохранены. :error',
		
		'map_visualization_chk' => 'Включить вид Карта',
        
        // Settings to enable/disable K-Link Public integration
        'klinkpublic_section' => 'Открытые в сети K-Link ',
		'klinkpublic_section_help' => 'Настроить доступ к Открытым документам сети K-Link',
		'klinkpublic_enabled' => 'Разрешить предоставление открытого доступа документам в сети K-Link',
		'klinkpublic_debug_enabled' => 'Разрешить отладку соединения K-Link',
		'klinkpublic_username' => 'Пользователь, используемый для опознования в открытой сети K-Link',
		'klinkpublic_password' => 'Пароль, используемый для опознования в открытой сети K-Link',
		'klinkpublic_url' => 'URL, используемый для исходного узла открытой сети K-Link ',
		
        // Settings to enable/disable UserVoice ticket integration
        'support_section' => 'Поддержка',
        'support_section_help' => 'Если у Вас есть подписка на поддержку команды разработчиков K-Link, пожалуйста, вставьте сюда маркер аутентификации для обеспечения ваших пользователей возможностью отправки запросов и получения помощи.',
		'support_token_field' => 'Маркер Поддержки',
        'support_save_btn' => 'Сохранить Настройки Поддержки',
	),

];
