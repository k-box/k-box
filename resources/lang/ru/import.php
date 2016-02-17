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

	'import_status_general' => '{0} импортирование завершено|{1} :num импортирование в прогрессе|[2,Inf] :num импортирование в прогрессе',

	'import_status_details' => ':total всего, :completed завершено и :executing в процессе',

	'form' => array(
		'submit_folder' => 'Папка для импорта',
		'submit_web' => 'Импортировать с веб',

		'select_web' => 'С URL',
		'select_folder' => 'С папки',

		'placeholder_web' => 'http(s)://какой-нибудь.com/файл.pdf',
		'placeholder_folder' => '/путь/к/какой-нибудь/папке',

		'help_web' => 'Пожалуйста, введите один url на одну строку. Веб адреса, которым нужна аутентификация, не поддерживаются.',
		'help_folder' => 'Network shares must be mounted on local filesystem, see <a href=":help_page_route" target="_blank">Помощь в импортировании</a>.',//I didn't understand the meaning of this phrase

	)

];
