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
		'indexing_error' => 'Документ не был индексирован в К-Линк',
		'private' => 'Личные',
		'shared' => 'Разделяемое',
		'is_public' => 'Общие Документы',
		'is_public_description' => 'This document is publicly available to other Institution in the K-Link Network',
		'trashed' => 'Этот документ находится в корзине',

	),

	'page_title' => 'Документы',

	'menu' => array(
		'all' => 'Все',
		'public' => 'Общие',
		'private' => 'Закрытые',
		'personal' => 'Личные',
		'starred' => 'Со звездочкой',
		'shared' => 'Разделяемые',
		'recent' => 'Текущие',
		'trash' => 'Корзина',
		'not_indexed' => 'Не индексированные',
	),


	'visibility' => array(
		'public' => 'Общие',
		'private' => 'Закрытые',
	),

	'type' => array(
		// See here for better understanding of the russian translation rules https://github.com/symfony/symfony/issues/8698
		// 'нет яблок|есть одно яблоко|есть %count% яблока|есть %count% яблок'
		// no apples | have one apple | have %count% apples | have %count% apples
		// 0 | 1-4 | 5+
		// 21 | 22-24 | 25+
		'web-page' => 'веб страница|веб страницы|веб страниц',
		'document' => 'документ|документа|документов',  
		'spreadsheet' => 'таблица|таблицы|таблиц',
		'presentation' => 'презентация|презентации|презентаций',
		'uri-list' => 'URL список|URL списка|URL списков',
		'image' => 'рисунок|рисунка|рисунков',
	),

	'empty_msg' => 'Нет документов в <strong>:context</strong>',

	'bulk' => array(

		'removed' => ':num элемент удален. Вы можете найти его в корзине.|:num элемента удалено. Вы можете найти их в корзине.|:num элементов удалено. Вы можете найти их в корзине.',
		
		'permanently_removed' => ':num элемент удален безвозвратно.|:num элемента удалено безвозвратно.|:num элементов удалено безвозвратно.',
		
		'restored' => ':num элемент восстановлен.|:num элемента восстановлена.|:num элементов восстановлено.',

		'remove_error' => 'Не возможно удалить элементы. Ни один документ или коллекция не были удалены. :error',
		
		'restore_error' => 'Не возможно восстановить документ. :error',
		
		'make_public' => ':num  документ был опубликован через Открытую Сеть К-Линк|:num документов доступны в Сети К-Линк.|:num документы доступны в Сети К-Линк.',
		
		'make_public_error' => 'Операция публикации не была завершена в связи с ошибкой. :error',

	),

	'create' => array(
		'page_breadcrumb' => 'Создать',
		'page_title' => 'Создать новый Документ',
	),

	'edit' => array(
		'page_breadcrumb' => 'Edit :документ',
		'page_title' => 'Edit :документ',

		'title_placeholder' => 'Название Документа',

		'abstract_label' => 'Абстракт',
		'abstract_placeholder' => 'Абстракт документа',

		'authors_label' => 'Авторы',
		'authors_help' => 'Авторы должны быть указаны через запятую со следующим форматом <code>имя фамилия &lt;mail@something.com&gt;</code>',

		'authors_placeholder' => 'Авторы документа (имя фамилия <mail@something.com>)',
		'language_label' => 'Язык',

		'last_edited' => 'Последнее изменение <strong>:time</strong>',
		'created_on' => 'Создано <strong>:time</strong>',
		'uploaded_by' => 'Загружено <strong>:name</strong>',

		'public_visibility_description' => 'Данный документ будет доступен для всех организаций участников Сети К-Линк',
		
		
		'not_index_message' => 'Документо не был успешно добавлен в К-Линк. Пожалуийста, попробуйте <button type="submit">Переиндексировать его</button> сейчас или свяжитесь с ваши администратором.',
	),

	'update' => array(
		'error' => 'Не возможно Обновить этот документ. Не было никаких изменений. :error',
	),


	'preview' => array(
		'page_title' => 'Предварительный просмотр :document',
		'error' => 'Извините, но мы не смогли загрузить предварительный просмотр ":document".',
		'not_available' => 'Предварительный  просмотр не возможен для данного документа.',
	),

	'versions' => array(

		'section_title' => 'Версии документов',

		'section_title_with_count' => '1 Версия документа|:number Версии документов|:number Версий документа',

		'version_count_label' => ':number версия|:number версии|:number версий',

		'version_number' => 'версия :number',

		'version_current' => 'текущий',

		'new_version_button' => 'Загрузить новую версию',
		
		'new_version_button_uploading' => 'Загрузка документа...',

		'filealreadyexists' => 'Версия загружаемого вами документа уже существует в DMS',
	),

	'messages' => array(
		'updated' => 'Документ обновлен',
		'local_public_only' => 'В настоящее время показаны только Открытые документы Организации.',
		'forbidden' => 'У вас нет прав для изменения документа.',
		// 'delete_forbidden' => 'Вы не можете удалять документы, пожалуйста, свжитесь с Контент Менеджером.',
		// 'delete_public_forbidden' => 'Вы не можете удалять Открытые Документы, пожалуйста, свяжитесь с Менеджером по Качеству содержания(Quality Content Manager).',
		// 'delete_force_forbidden' => 'Вы не можете безвозвратно удалить документ. Пожалуйста, свяжитесь с Менеджером по Качеству содержания(Quality Content Manager).',
		'delete_forbidden' => 'Вы не можете удалять документы, пожалуйста, свяжитесь с Проектным менеджером или с Администратором.',
		'delete_public_forbidden' => 'Вы не можете удалить публичный документ, пожалуйста, свяжитесь с участником К-Линк или Администратором.',
		'delete_force_forbidden' => 'Вы не можете безвозвратно удалить документ. Пожалуйста, свяжитесь с Проектным менеджером или с Администратором.',
		'drag_hint' => 'Перебросьте файлы сюда для того чтобы начать загрузку.',
		'recent_hint_dms_manager' => 'Вы видите все обновления документов введенные каждым пользователем СУД.',
	),
];
