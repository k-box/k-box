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
	
	'preparing_import' => 'Preparing import...',

	'form' => array(
		'submit_folder' => 'Import folder',
		'submit_web' => 'Import from web',

		'select_web' => 'From URL',
		'select_folder' => 'From Folder',

		'placeholder_web' => 'http(s)://somesite.com/file.pdf',
		'placeholder_folder' => '/path/to/a/folder',

		'help_web' => 'Please insert one url per line. Web address that needs authentication are not supported.',
		'help_folder' => 'Network shares must be mounted on local filesystem, see <a href=":help_page_route" target="_blank">Import Help</a>.',

	),
	
	/**
	 * Possible import status
	 */
	'status' => [
		// The import is in the queue and waits for being processed
		'queued' => 'Queued',
		// The import is put on hold
		'paused' => 'Paused',
		// The import is downloading the files
		'downloading' => 'Download in progress',
		// The import is completed
		'completed' => 'Completed',
		// The documents imported are in the search engine indexing phase
		'indexing' => 'Getting ready for search',
		// Import has an error
		'error' => 'Error',
	],
	
	'remove' => [
		'remove_btn' => 'Remove',
		'remove_btn_hint' => 'Removes the import',
		'remove_dialog_title' => 'Remove ":import"?',
		'remove_confirmation' => 'You want to remove ":import"?',
		'removing' => 'Removing ":import"...',
		'removing_alt' => 'Removing...',
		'removed_message' => '":import" has been removed from the import list.',
		
		// message showed when a user wants to remove an import created by another user
		'destroy_forbidden_user' => 'You cannot remove ":import" from the import list because you are not the creator of the import.',
		// This version is used when the import filename cannot be retrieved because the file is deleted
		'destroy_forbidden_user_alternate' => 'You cannot remove the import because you are not the creator of the it.',
		
		// message showed when the remove action has been requested on import with a status different than "completed" or "error"
		'destroy_forbidden_status' => 'You cannot remove imports that are pending or in downloading state.',
		
		// General error when something not-expected happen
		'destroy_error' => 'The import cannot be removed. If the problem persists please report this message to the support: ":error"',
		'destroy_error_dialog_title' => 'The import cannot be removed',
	],
	
	'retry' => [
		'retry_btn' => 'Retry',
		'retry_btn_hint' => 'Try to execute the import again',
		'retrying' => 'Adding back ":import"...', // the import can only be added back to the queue of the imports
		'retrying_alt' => 'Retrying...',
		'retry_completed_message' => '":import" has been added back to the queue of the current imports.',
		
		// message showed when a user wants to retry an import created by another user
		'retry_forbidden_user' => 'You cannot retry the import of ":import" because you are not the creator of the import.',
		// This version is used when the import filename cannot be retrieved because the file is deleted
		'retry_forbidden_user_alternate' => 'You cannot retry the import because you are not the creator of the it.',
		
		'retry_error_file_not_found' => 'Was not possible to retry the import because the original data was deleted',
		
		'retry_forbidden_status' => 'You cannot retry imports that are not blocked due to an error.',
		
		// General error when something not-expected happen
		'retry_error' => 'The import cannot be retried. If the problem persists please report this message to the support: ":error"',
		'retry_error_dialog_title' => 'Cannot retry',
	],
	

];
