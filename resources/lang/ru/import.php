<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Document Import page Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used on the Import documents page
	|
	*/

	'page_title' => 'Импорт',

	'clear_completed_btn' => 'Очистка завершена',

	'import_status_general' => '{0} импортирование завершено| :num импортирование в прогрессе| :num импортирования в прогрессе| :num импортирований в прогрессе',

	'import_status_details' => ':total всего, :completed завершено и :executing в процессе',

	'form' => array(
		'submit_folder' => 'Папка для импорта',
		'submit_web' => 'Импортировать с веб',

		'select_web' => 'С URL',
		'select_folder' => 'Из папки',

		'placeholder_web' => 'http(s)://названиесайта.com/файл.pdf',
		'placeholder_folder' => '/путь/к/какой-нибудь/папке',

		'help_web' => 'Пожалуйста, введите не больше одного url на строку. Веб адреса, которым нужна аутентификация, не поддерживаются.',
		'help_folder' => 'Сетевые папки должны находиться на локальной файловой системе, как указано в <a href=":help_page_route" target="_blank">Помощи по импортированию</a>.',

	)

];
