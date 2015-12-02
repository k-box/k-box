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

	'import_status_general' => '{0} импорт завершен|{1} :num импортирование в прогрессе|[2,Inf] :num импортирование в прогрессе',

	'import_status_details' => ':total всего, :completed завершено и :executing в прцессе',

	'form' => array(
		'submit_folder' => 'Папка для импорта',
		'submit_web' => 'Импортировть с веб',

		'select_web' => 'С URL',
		'select_folder' => 'С папки',

		'placeholder_web' => 'http(s)://somesite.com/file.pdf',
		'placeholder_folder' => '/path/to/a/folder',

		'help_web' => 'Пожалуиста, введите один url на одну строку. Веб адрес, которым нужна аутентификация, не поддерживаются.',
		'help_folder' => 'Network shares must be mounted on local filesystem, see <a href=":help_page_route" target="_blank">Помощь в импортировании</a>.',//I didn't understand the meaning of this phrase

	)

];
