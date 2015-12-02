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

	'page_title' => 'Import',

	'clear_completed_btn' => 'Clear completed',

	'import_status_general' => '{0} Import completed|{1} :num import in progress|[2,Inf] :num imports in progress',

	'import_status_details' => ':total total, :completed completed and :executing in progress',

	'form' => array(
		'submit_folder' => 'Import folder',
		'submit_web' => 'Import from web',

		'select_web' => 'From URL',
		'select_folder' => 'From Folder',

		'placeholder_web' => 'http(s)://somesite.com/file.pdf',
		'placeholder_folder' => '/path/to/a/folder',

		'help_web' => 'Please insert one url per line. Web address that needs authentication are not supported.',
		'help_folder' => 'Network shares must be mounted on local filesystem, see <a href=":help_page_route" target="_blank">Import Help</a>.',

	)

];
