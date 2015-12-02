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

	'unknown' => 'Unknown generic error in the request',

	'upload' => array(
		'simple' => 'Upload error :description',
		'filenamepolicy' => 'The file :filename is not respecting the naming convetion.',
		'filealreadyexists' => 'The file :filename already exists.',
	),

	'group_already_exists_exception' => array(
		'only_name' => 'A collection named ":name" already exists.',
		'name_and_parent' => 'The collection ":name" under ":parent" already exists.',
	),


	'reindex_all' => 'The reindex all procedure cannot be completed due to an error. See the logs or contact the administrator.',

	'token_mismatch_exception' => 'Seems that your session is somehow expired, please refresh the page and then continue with your work. Thanks.',

	'not_found' => 'The resource you are looking for cannot be found.',
	
	'document_not_found' => 'The document you are looking for cannot be found or was deleted.',

	'forbidden_exception' => 'You don\'t have access to the page.',
	
	'fatal' => 'Fatal error :reason',

	'panels' => array(
		'title' => 'Oops! something unexcepted has happened.',
		'prevent_edit' => 'You cannot edit :name'
	),

	'import' => array(
		'folder_not_readable' => 'The folder :folder is not readable. Make sure to have read permission.',
	),


	'group_edit_institution' => "You cannot edit institution level groups.",
	'group_edit_else' => "You cannot edit someone else groups.",

	'503_title' => 'K-Link DMS Maintenance',
	'503_text' => 'The <strong>DMS</strong> is currently in<br/><strong>maintenance</strong><br/><small>will be back shortly :)</small>',

	'500_title' => 'Error - K-Link DMS',
	'500_text' => 'Oh Snap! Something <strong>bad</strong><br/>and unexpected <strong>happened</strong>,<br/>we are deeply sorry.',

	'404_title' => 'Not Found on the K-Link DMS',
	'404_text' => 'Woops! Looks like that <strong>the page</strong><br/>your are looking for<br/><strong>doesn\'t exists</strong> anymore.',
	
	'401_title' => 'You cannot view the page K-Link DMS',
	'401_text' => 'Woops! Looks like you can<strong>not</strong> view the page<br/>due to your <strong>Authorization</strong> level.',

	'405_title' => 'Method Not Allowed on the K-Link DMS',
	'405_text' => 'Don\'t call me like this again.',
	
	'413_title' => 'Document Excessive File Size',
	'413_text' => 'The file your are trying to upload exceeds the maximum allowed file size.',
	
	'klink_exception_title' => 'K-Link Services Error',
	'klink_exception_text' => 'There were a problem connecting to the K-Link Services.',
];
