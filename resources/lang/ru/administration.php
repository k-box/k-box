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

		'accounts'=>'Учетная запись',
		'language'=>'Язык',
		'storage'=>'Хранилище',
		'network'=>'Сеть',
		'mail'=>'Почта',
		'update'=>'Обновление и восстановление',
		'maintenance'=>'Обслуживание и События',

	),

	'accounts' => array(

		'disable_confirm' => 'Вы действительно хотите запретить :name',

		'create_user_btn' => 'Создать пользователя',

		'table' => array(

			'name_column' => 'имя',
			'email_column' => 'почта',
			'institution_column' => 'инструкция',

		),
		
		'edit_account_title' => 'Изменить :name',

		'labels' => array(

			'email' => 'Почта',
			'username' => 'Логин',
			'perms' => 'Разрешения',

			'cancel' => 'Отменить',

			'create' => 'Создать',
			'update' => 'Обновить',

			'institution' => 'Институт',
			'select_institution' => 'Выберите к какой организации относится пользователь...',

		),

		'capabilities' => array(

			'manage_dms' => 'Пользователь имеет доступ к управляющей панели СУД',
			'manage_dms_users' => 'Пользователь может управлять пользователями СУД',
			'manage_dms_log' => 'Пользователь может видеть журнал СУД',
			'manage_dms_backup' => 'Пользователь может совершить резервное копирование и восстановление СУД',
			'change_document_visibility' => 'Пользователь может изменить внешний вид документов',
			'edit_document' => 'Пользователь может править документы',
			'delete_document' => 'Пользователь может удалять документы',
			'import_documents' => 'Пользователь может импортировать документы с папок или внешний URL',
			'upload_documents' => 'Пользователь может загружать документы',
			'make_search' => 'Пользователь имеет доступ ко всем закрытым документам организации',
			'manage_own_groups' => 'Пользователь может управлять личной коллекцией документов',
			'manage_institution_groups' => 'Пользователь может управлять коллекцией документов организации',
			
			'manage_share' => 'Пользователь может делиться с закрытыми документами с другим пользователем или группой пользователей',
			'receive_share' => 'Пользователь может просмотреть документы разделяемые документы',
		
			'manage_share_personal' => 'Пользователь может делиться с личными докумен с одним пользователем или "личной" группой пользователей',
			'manage_share_private' => 'Пользователь может делиться документами с группой пользователей определенных на организационном уровне',
			
			'clean_trash' => 'Пользователь иммет право на безвозвратное удаление докуентов',
			
			'manage_personal_people' => 'Пользователь может создать/изменять группы пользователей определенных на личном уровне',
			'manage_people' => 'Пользователь может создать/изменять группы пользователей определенных на организационном уровне',

		),
		
		'types' => array(

			'guest' => 'Гость',
	        'partner' => 'Партнер',
	        'content_manager' => 'Контент менеджер',
	        'quality_content_manager' => 'Качественный Контент Менеджер',
	        'project_admin' => 'Администратор проекта',
	        'admin' => 'Администратор К-Бокс',
	        'klinker' => 'К-Линкер',

		),

		'create' => array(

			'title' => 'Создать новую учетную запись',
			'slug' => 'Создать',

		),

		'created_msg' => 'Пользователь создан, пароль был отправлен лично пользователю на почту',
		'disabled_msg' => 'Пользователь :name отключен',
		'enabled_msg' => 'Пользователь :name восстановлен',
		'updated_msg' => 'Пользователь обновлен',
		'mail_subject' => 'Ваша учетная запись СУД К-Линк готова',
		'reset_sent' => 'Письмо о сбросе пароля было отправлено на :name (:email)',
		'reset_not_sent' => 'Письмо о сбросе пароля не может быть отправлено на :email. :error',
		'reset_not_sent_invalid_user' => 'Пользователь, :email, не может быть найден.',
		'send_reset_password_btn' => 'Сброс пароля',
		'send_reset_password_hint' => 'Запросить ссылку на изменение пароля пользователем',
		'send_message_btn' => 'Отправить сообщение',
		'send_message_btn_hint' => 'Отправить сообщение каждому пользователю',
	),

	'language' => array(

		'list_label' => 'Здесь указаны поддерживаемые языки.',
		'code_column' => 'Язык кода',
		'name_column' => 'Язык имени',

	),

	'storage' => array(

		'disk_status_title' => 'Статус диска',
		'documents_report_title' => 'Тип документа',
		'disk_number' => 'Диск :number',
		'disk_type_all' => 'Главный и диск документов',
		'disk_type_main' => 'Главный диск',
		'disk_type_docs' => 'Диск документов',
		'disk_space' => ':free <strong>свободно</strong>, :used использовано of :total всего.',

		'reindexall_btn' => 'Переиндексировать все документы',

		'reindexing_status' => 'Переиндексация :number документов...',
		'reindexing_all_status' => 'Переиндексация всех документов...',
		'reindexing_status_completed' => 'Всш локументы были переиндексированы.',

		'naming_policy_title' => 'Правила наименований документов',
		'naming_policy_description' => 'Вы можете запретить загрузку файлов не отвечающие правилам наименований документов',

		'naming_policy_btn_activate' => 'Включить правила наименований докуементов',
		'naming_policy_btn_save' => 'Обновить',
		'naming_policy_btn_deactivate' => 'Отключить правила наименований документов',

		'naming_policy_msg_activated' => 'Правила наименований включенв',
		'naming_policy_msg_deactivated' => 'Правила наименований выключены',

	),

	'network' => array(

		'klink_net_title' => 'К-Линк сетевое соединение',

		'net_cards_title' => 'Сетевой интерфейс',

		'no_cards' => 'Сетевое соединение не обнаружено.',

		'cards_problem' => 'Проблема с чтением информации с сетевой карты. Здесь подробный ответ разработсика', //I didn't understand the meaning of the message

		'current_ip' => 'Текущий IP адрес :ip',

		'klink_status' => array(
			'success' => 'Установлено и верифициравано',
			'failed' => 'Не может соединиться с К-Линк Корнем',
		)

	),
	'mail' => array(
		'save_btn' => 'Сохранить настройки почты',
		'configuration_saved_msg' => 'Настройки почты были успешно сохранены.',
		'test_success_msg' => 'Тестовое сообщение было удачно отправлено (от :from). Проверьте входящие письма.',
		'test_failure_msg' => 'Тестовое сообщение не может быть отправлено в связ и с ошибкой.',
		'enable_chk' => 'Разрешить отправку писем',
		'test_btn' => 'Отправить тестовое сообщение',
		'from_label' => 'Глобальный "От" Адрес',
		'from_description' => 'Здесь, вы можете указать имя и адрес используемые глобально для всех сообщений отправленных К-Линк СУД.',
		'from_name_placeholder' => 'Имя (например, Джон)',
		'from_address_placeholder' => 'Почта (например, john@klink.org)',
		'host_label' => 'SMTP Host Address',
		'port_label' => 'SMTP Host Port',
		'encryption_label' => 'E-Mail Encryption Protocol',
		'username_label' => 'SMTP Server Пользователь',
		'password_label' => 'SMTP Server Пароль',
	),
	'update' => array(),
	'maintenance' => array(

		'queue_runner' => 'Asynchronous process jobs runner',

		'queue_runner_started' => 'Started and listening',
		'queue_runner_stopped' => 'Не запускается',

		'queue_runner_not_running_description' => 'The jobs runner is not running so Mail Messages and Document Indexing may not work as expected.',
		
		'logs_widget_title' => 'Latest Log entries', 
	),

];
