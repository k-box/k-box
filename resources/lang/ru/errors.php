<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Generic Errors Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used inside for rendering error messages
	|
	*/

	'unknown' => 'Неизвестная ошибка в запросе',

	'upload' => array(
		'simple' => 'Ошибка загрузки :description',
		'filenamepolicy' => 'Файл :filename не отвечает правилам наименования документов.',
		'filealreadyexists' => 'Файл :filename уже существует.',
	),

	'group_already_exists_exception' => array(
		'only_name' => 'Коллекция с таким именем ":name" уже существует.',
		'name_and_parent' => 'Коллекция ":name" под ":parent" уже существует.',
	),

	'generic_text' => 'Опаньки! Произошло что-то неожиданное.',
    'generic_text_alt' => 'Опаньки! Произошло что-то неожиданное. :error',
    'generic_title' => 'Опаньки!',

	'reindex_all' => 'Переиндексация всех процедур не может быть завершена в связи с ошибкой. Посмотрите журнал ошибок или свяжитесь с администратором.', //logs as list of errors

	'token_mismatch_exception' => 'Время вашей сессии истекло, пожалуйста, обновите страницу для продолжения работы. Спасибо.',

	'not_found' => 'Ресурс не найден.',
	
	'document_not_found' => 'Нужный вам документ не может быть найден или был удален.',

	'forbidden_exception' => 'У вас нет доступа к странице.',
	'forbidden_edit_document_exception' => 'Вы не можете редактировать документ.',
	'forbidden_see_document_exception' => 'Вы не можете просмотреть документ, т.к. он в личном пользовании.',
	
	'fatal' => 'Фатальная ошибка :reason',

	'panels' => array(
		'title' => 'Ой! Непредвиденная ошибка.',
		'prevent_edit' => 'Вы не можете редактировать :name',
	),

	'import' => array(
		'folder_not_readable' => 'Папка :folder недоступна для чтения. Убедитесь что у вас есть права на чтение.',
		'url_already_exists' => 'Файл из вебсайта с тем же url (:url) уже был импортирован.',
		'download_error' => 'Документ ":url" не может быть загружен. :error',
	),


	'group_edit_institution' => "Вы не можете редактировать группы, принадлежащие организации.",
	'group_edit_else' => "Вы не можете вносить изменения в чужую группу.",

	'503_title' => 'Обслуживание K-LINK DMS',
	'503_text' => '<strong>DMS</strong> в данный момент находится<br/><strong>на техническом обслуживании</strong><br/><small>Мы скоро вернемся :)</small>',

	'500_title' => 'Ошибка - K-LINK DMS',
	'500_text' => 'Опаньки! Что-то <strong>плохое</strong><br/>и неожиданное <strong>произошло</strong>,<br/>мы жутко извиняемся.',

	'404_title' => 'Не найден в K-LINK DMS',
	'404_text' => 'Ой! <strong>Страница,</strong><br/>искомая вами,<br/>больше<strong>не существует</strong>.',
	
	'401_title' => 'Вы не можете просмотреть страницу K-LINK DMS',
	'401_text' => 'Ой! Скорее всего вы <strong>не можете</strong> просмотреть страницу<br/>из-за вашей <strong>Авторизации</strong>.',
	
	'403_title' => 'У вас нет доступа для просмотра данной страницы.',
	'403_text' => 'Опаньки! Кажется, вы <strong>не</strong> можете просматривать данную страницу <br/>из-за вашей<strong>Авторизации</strong>.',

	'405_title' => 'Данный метод не разрешен на K-LINK DMS',
	'405_text' => 'Не называй меня больше так.',
	
	'413_title' => 'Чрезмерно большой размер документа',
	'413_text' => 'Файл, который вы пытаетесь загрузить, превышает максимально допустимый размер.',

	'klink_exception_title' => 'Ошибка сервисов K-LINK.',
	'klink_exception_text' => 'Ошибка в подключении к сервисам K-LINK.',
	
	'reindex_failed' => 'Search might not be up-to-date with latest changes, please consult the support team for more information.',
    
    'page_loading_title' => 'Проблема загрузки', 
    'page_loading_text' => 'Загрузка страницы кажется замедленной и некоторая функциональность может быть недоступна. Пожалуйста, обновите страницу.',
    
    'dragdrop' => [
        'not_permitted_title' => 'На данный момент перетаскивание недоступно',
        'not_permitted_text' => 'Вы не можете совершить перетаскивание.',
        'link_not_permitted_title' => 'Перетаскивание ссылок недоступно.',
        'link_not_permitted_text' => 'В настоящее время вы не можете перетаскивать ссылки на вебсайты.',
    ],
];
