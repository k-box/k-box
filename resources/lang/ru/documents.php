<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Document and Document Descriptor Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used for localizing the document description
	| meta information and the document administration menu and title
	|
	*/

	'descriptor' => array(

		'owned_by' => 'владелец',
		'language' => 'язык',
		'added_on' => 'добавлено',
		'last_modified' => 'Изменено',
		'indexing_error' => 'Документ не был индексирован в K-LINK',
		'private' => 'Личные',
		'shared' => 'Совместный доступ',
		'is_public' => 'Открытый доступ',
		'is_public_description' => 'Документ доступен для других Организаций в сети K-LINK',
		'trashed' => 'Документ находится в корзине',
        'klink_public_not_mine' => 'Нельзя внести изменения, т.к. данный файл является всего лишь ссылкой на документ во открытомем доступе в K-Link сети.',
	),

	'page_title' => 'Документы',

	'menu' => array(
		'all' => 'Все',
		'public' => 'Открытые в K-Link сети',
		'private' => 'Закрытые',
		'personal' => 'Личные',
		'starred' => 'Со звездочкой',
		'shared' => 'Доступные мне',
		'recent' => 'Недавние',
		'trash' => 'Корзина',
		'not_indexed' => 'Неиндексированные',
		'recent_hint' => 'Здесь показаны последние ваши рабочие документы',
        'starred_hint' => 'Здесь показаны важные или интересные вам документы',
	),


	'visibility' => array(
		'public' => 'Открытые',
		'private' => 'Закрытые',
	),

	'type' => array(
		// See here for better understanding of the russian translation rules https://github.com/symfony/symfony/issues/8698
		// 'нет яблок|есть одно яблоко|есть %count% яблока|есть %count% яблок'
		// no apples | have one apple | have %count% apples | have %count% apples
		// 0 | 1-4 | 5+
		// 21 | 22-24 | 25+
        /**
		
		- If the number is 1, or the number ends in the word 1 (example: 1, 21, 61) (but not 11), then you should use the first case
		- If the number, or the last digit of the number is 2, 3 or 4, (example: 22, 42, 103, 4) (but not 12, 13 & 14), then you should use the second case
		- If the number ends in any other digit you should use the 3rd case. All the 'teens'  fit in to this catagory (11, 12, 13, 14, 15,16,17,18,19). Any number ending with 0 (including 0 itself) also fits into this category
*/
		'web-page' => 'веб страница|веб страницы|веб страниц',
		'document' => 'документ|документа|документов',  
		'spreadsheet' => 'таблица|таблицы|таблиц',
		'presentation' => 'презентация|презентации|презентаций',
		'uri-list' => 'URL список|URL списка|URL списков',
		'image' => 'рисунок|рисунка|рисунков',
		'geodata' => 'геоданные|геоданных|геоданных',
        'text-document' => 'текстовый документ|текстовых документа|текстовых документов',
	),

	'empty_msg' => 'Нет документов в <strong>:context</strong>',

	'bulk' => array(

		'removed' => ':num элемент удален. Вы можете найти его в корзине.|:num элемента удалено. Вы можете найти их в корзине.|:num элементов удалено. Вы можете найти их в корзине.',
		
		'permanently_removed' => ':num элемент удален безвозвратно.|:num элемента удалено безвозвратно.|:num элементов удалено безвозвратно.',
		
		'restored' => ':num элемент восстановлен.|:num элемента восстановлено.|:num элементов восстановлено.',

		'remove_error' => 'Невозможно удалить элементы. Ни один документ или коллекция не были удалены. :error',
		
        'copy_error' => 'Невозможно копировать в коллекцию. :error',
		
        'copy_completed_all' => 'Все документы были добавлены в :collection',
		
        // used when not all the documents you were adding to a collection has been added
        'copy_completed_some' => '{0}Ни один документ не был добавлен, т.к. они уже хранились в ":collection"|[1,Inf] Добавленых документов :count, оставшиеся :remaining уже находились в :collection',
        
		'restore_error' => 'Невозможно восстановить документ. :error',
		
		'make_public' => ':num документ находится в открытом доступе в сети K-LINK|:num документа находятся в открытом доступе в сети K-LINK.|:num документов находятся в открытом доступе в сети K-LINK.',
		
		'make_public_error' => 'Операция предоставления открытого доступа не была завершена в связи с ошибкой. :error',
		'make_public_error_title' => 'Невозможно сделать открытым для K-Link сети',
        
        'make_public_success_text_alt' => 'Документы находятся в открытом доступе сети K-Link',
		'make_public_success_title' => 'Операция предоставления открытого доступа успешно завершена',

        'adding_title' => 'Добавление документов...',
        'adding_message' => 'Пожалуйста, подождите, ваши документы добавляются в коллекцию...',
        'added_to_collection' => 'Добавлено',
        
        'add_to_error' => 'Невозможно добавить в коллекцию',
        
        'making_public_title' => 'Предоставляю открытый доступ...',
        'making_public_text' => 'Пожалуйста, подождите, к вашим документам предоставляется открытый доступ в K-Link сети',
    
        'make_public_change_title_not_available' => 'В данный момент возможность изменения названия до предоставления открытого доступа к файлу недоступна.',

        'make_public_all_collection_dialog_text' => 'Вы сделаете все документы данной коллекции открытыми для K-Link сети. (жмите вне сообщения для отмены)',
        'make_public_inside_collection_dialog_text' => 'ы сделаете все документы коллекции  ":item" открытыми для K-Link сети. (жмите вне сообщения для отмены)',
        
        'make_public_dialog_title' => 'Сделать открытым ":item" для K-Link сети',
        'make_public_dialog_title_alt' => 'Сделать открытым для K-Link сети',
        
        'publish_btn' => 'Сделать открытым!',
        'make_public_empty_selection' => 'Пожалуйста, выберите документы, чтобы сделать их открытыми для K-Link сети.',
        
        'make_public_dialog_text' => 'Документ ":item" будет в открытом доступе для K-Link сети. (жмите вне сообщения для отмены)',
        'make_public_dialog_text_count' => 'Вы сделаете :count документов открытыми для K-Link сети. (жмите вне сообщения для отмены)',
	),

	'create' => array(
		'page_breadcrumb' => 'Создать',
		'page_title' => 'Создать новый документ',
	),

	'edit' => array(
		'page_breadcrumb' => 'Изменить :document',
		'page_title' => 'Изменить :document',

		'title_placeholder' => 'Название документа',

		'abstract_label' => 'Краткое содержание',
		'abstract_placeholder' => 'Краткое содержание',

		'authors_label' => 'Авторы',
		'authors_help' => 'Авторы должны быть указаны через запятую со следующим форматом <code>имя фамилия &lt;mail@something.com&gt;</code>',
		'authors_placeholder' => 'Авторы документа (имя фамилия <mail@something.com>)',
		
		'language_label' => 'Язык',

		'last_edited' => 'Последнее изменение <strong>:time</strong>',
		'created_on' => 'Создано <strong>:time</strong>',
		'uploaded_by' => 'Загружено <strong>:name</strong>',

		'public_visibility_description' => 'Данный документ будет доступен для всех участников Сети K-LINK',
		
		
		'not_index_message' => 'Документ недоступен в сети K-LINK. Пожалуйста, попробуйте <button type="submit">переиндексировать его</button> сейчас или свяжитесь с вашим администратором.',
	),

	'update' => array(
		'error' => 'Невозможно обновить этот документ. Не было никаких изменений. :error',
		
		'removed_from_title' => 'Снять с коллекции',
        'removed_from_text' => 'Документ был снят с ":collection"',
        'removed_from_text_alt' => 'Документ был снят с коллекции',
        
        'cannot_remove_from_title' => 'Невозможно снять с коллекции',
        'cannot_remove_from_general_error' => 'Невозможно снять с коллекции. При повторении ошибки, пожалуйста, свяжитесь с Администратором DMS.',

	), 
	
	'restore' => [
        
        'restore_dialog_title' => 'Восстановить :document?',
        'restore_dialog_text' => 'Вы собираетесь восстановить ":document"',
        'restore_dialog_title_count' => 'Восстановить :count документов?',
        'restore_dialog_text' => 'Вы собираетесь восстановить ":document"',
        'restore_dialog_text_count' => 'Вы собираетесь восстановить :count элементов',
        'restore_dialog_yes_btn' => 'Да, восстановить!',
        'restore_dialog_no_btn' => 'Нет, отменить!',
        
        'restore_success_title' => 'Восстановить',
        'restore_error_title' => 'Не смог восстановить',
        'restore_error_text_generic' => 'К сожалению, я не смог переместить из корзины нужный элемент.',
      
        'restoring' => 'Восстанавливаю...',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Удалить ":document"?',
        'dialog_title_alt' => 'Удалить документ?',
        'dialog_title_count' => 'Удалить :count документов?',
        'dialog_text' => 'You\'re about to delete :document.',
        'dialog_text_count' => 'Вы собираетесь удалить :count документов',
        'deleted_dialog_title' => ':document был удален',
        'deleted_dialog_title_alt' => 'Удалено',
        'cannot_delete_dialog_title' => 'Невозможно удалить ":document"!',
        'cannot_delete_dialog_title_alt' => 'Удаление невозможно!',
        'cannot_delete_general_error' => 'Произошла ошибка. Пожалуйста, свяжитесь с Администратором.',
    ],


	'preview' => array(
		'page_title' => 'Предварительный просмотр :document',
		'error' => 'Извините, мы не смогли загрузить предварительный просмотр ":document".',
		'not_available' => 'Предварительный  просмотр невозможен для данного документа.',
		'google_file_disclaimer' => ':document это файл с Google Диска, поэтому вы не можете просмотреть его здесь. Откройте его на Google Диске.',
    	'open_in_google_drive_btn' => 'Открыть в Google Диске',
	),

	'versions' => array(

		'section_title' => 'Версии документов',

		'section_title_with_count' => '1 Версия документа|:number Версии документов|:number Версий документа',

		'version_count_label' => ':number версия|:number версии|:number версий',

		'version_number' => 'версия :number',

		'version_current' => 'текущая',

		'new_version_button' => 'Загрузить новую версию',
		
		'new_version_button_uploading' => 'Загрузка документа...',

		'filealreadyexists' => 'Версия загружаемого вами документа уже существует в системе',
	),

	'messages' => array(
		'updated' => 'Документ обновлен',
		'local_public_only' => 'В настоящее время показаны только открытыеие документы Организации.',
		'forbidden' => 'У вас нет прав для изменения документа.',
		'delete_forbidden' => 'Вы не можете удалять документы. Пожалуйста, свяжитесь с Проектным менеджером или с Администратором.',
		'delete_public_forbidden' => 'Вы не можете удалить документ, находящийся в открытом доступе. Пожалуйста, свяжитесь с K-LINKером или с Администратором.',
		'delete_force_forbidden' => 'Вы не можете безвозвратно удалить документ. Пожалуйста, свяжитесь с Проектным менеджером или с Администратором.',
		'drag_hint' => 'Перетащите файлы сюда для начала загрузки.',
		'recent_hint_dms_manager' => 'Вы видите все обновления документов, введенные каждым пользователем системы.',
        'no_documents' => 'Нет документов. Вы можете загрузить новые документы, перетаскивая их сюда или используя "Создать или добавить" в верхнем навигационном поле.',
	),
	
	 'trash' => [
        
        'clean_title' => 'Очистить корзину?',
        'yes_btn' => 'Да, очистить!',
        'no_btn' => 'Нет, отменить!',
        
        'empty_all_text' => 'Все документы будут безвозвратно удалены из корзины. Данное действие удалит файлы, версии, отмеченные звездочкой, коллекции и совместные доступы, созданные пользователями. Данное действие не может быть отменено позже.',
        'empty_selected_text' => 'Вы собираетесь безвозвратно удалить из корзины выбранные документы. Вы также удалите файлы, версии, отмеченные звездочкой, коллекции и совместные доступы. Данное действие не может быть отменено позже.',
        
        'cleaned' => 'Корзина очищена',
        'cannot_clean' => 'Невозможно очистить корзину',
        'cannot_clean_general_error' => 'Произошла ошибка при очистке корзины. При повторении ошибки, пожалуйста, свяжитесь с Администратором.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Ваш браузер не поддерживает перетаскивание папок.',
        'error_dialog_title' => 'Ошибка Загрузки Файла',
        
        'max_uploads_reached_title' => 'Простите, вам придется немного подождать',
        'max_uploads_reached_text' => 'Мы можем обрабатывать пока только маленькие файлы. Пожалуйста, проявите немного терпения перед очередным добавлением файлов.',
        
        'all_uploaded' => 'Все файлы были успешно загружены.',
		
		'upload_dialog_title' => 'Загрузка',
		'dragdrop_not_supported' => 'Ваш браузер не поддерживает функцию загрузки файлов с помощью перетаскивания.',
		'dragdrop_not_supported_text' => 'Пожалуйста, используйте выбор файлов в "Создать или добавить".',
		'remove_btn' => "Удалить",
		'cancel_btn' => 'Отменить загрузку',
		'cancel_question' => 'Вы уверенны, что хотите отменить загрузку?',
    ],
];
