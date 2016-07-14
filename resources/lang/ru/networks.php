<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Networks Language Lines
	|--------------------------------------------------------------------------
	|
	| contains messages for localizing actions on different public networks
	| 
	| original strings taken from
	| - actions.make_public
	| - actions.publish_documents
	| - documents.bulk.making_public_title
	| - documents.bulk.making_public_text
	| - documents.bulk.make_public_error
	| - documents.bulk.make_public_error_title
	| - documents.bulk.make_public_success_text_alt
	| - documents.bulk.make_public_success_title
	| - documents.bulk.make_public_change_title_not_available
	| - documents.bulk.make_public_all_collection_dialog_text
	| - documents.bulk.make_public_inside_collection_dialog_text
	| - documents.bulk.make_public_dialog_title
	| - documents.bulk.make_public_dialog_title_alt
	| - documents.bulk.publish_btn
	| - documents.bulk.make_public_empty_selection
	| - documents.bulk.make_public_dialog_text
	| - documents.bulk.make_public_dialog_text_count
	| 
	|
	*/

	'klink_network_name' => 'Открытые в сети K-Link',
	
	'menu_public' => ':network',
	'menu_public_hint' => 'Узнайте какие документы доступны в сети :network',

	'make_public' => 'Сделать открытым',
	'publish_to_short' => 'Предоставить открытый доступ',
	'publish_to_long' => 'Предоставить доступ в сети :network',

	
	'publish_to_hint' => 'Выберите документы для предоставления к ним доступа в сети :network',
    

	'publish_btn' => 'Сделать открытым!',

	'settings' => [
		'section' => 'Присоединиться к сети',
		'section_help' => 'Конфигурировать доступ вашего DMS к сети',
		'enabled' => 'Разрешить предоставление к документам открытого доступа в сети',
		'debug_enabled' => 'Разрешить отыскание и устранение неполадок в соединении к сети',
		'username' => 'Имя пользователя, используемое для удостоверенности при соединении к Сети',
		'password' => 'Пароль, используемый для удостоверенности при соединении к Сети',
		'url' => 'Адрес URL Точки Входа в Сеть',
	],


	'made_public' => ':num документ находится в открытом доступе в :network|:num документа находятся в открытом доступе в :network.|:num документов находятся в открытом доступе в :network.',
		
	'make_public_error' => 'Операция предоставления открытого доступа не была завершена в связи с ошибкой. :error',
	'make_public_error_title' => 'Невозможно добавить в открытую :network',
	
	'make_public_success_text_alt' => 'Документы находятся в открытом доступе в :network',
	'make_public_success_title' => 'Операция предоставления открытого доступа успешно завершена',

	'making_public_title' => 'Предоставляю открытый доступ в :network...',
	'making_public_text' => 'Пожалуйста, подождите, к вашим документам предоставляется открытый доступ в :network',

	'make_public_change_title_not_available' => 'Изменение названия до предоставления к файлу открытого доступа недоступно.',

	'make_public_all_collection_dialog_text' => 'Вы сделаете все документы данной коллекции открытыми для :network (жмите вне сообщения для отмены)',
	'make_public_inside_collection_dialog_text' => 'Вы сделаете все документы коллекции  ":item" открытыми для :network (жмите вне сообщения для отмены)',
	
	'make_public_dialog_title' => 'Сделать открытым ":item" в :network',
	'make_public_dialog_title_alt' => 'Сделать открытым в :network',
	
	
	'make_public_empty_selection' => 'Пожалуйста, выберите документы, чтобы сделать их открытыми для :network.',
        
	'make_public_dialog_text' => 'Документ ":item" будет в открытом доступе в :network (жмите вне сообщения для отмены)',
	'make_public_dialog_text_count' => 'Вы сделаете :count документов открытыми в :network (жмите вне сообщения для отмены)',

];
