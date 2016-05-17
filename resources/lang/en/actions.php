<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Actions Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used primarly for buttons and switcher
	|
	*/

	'edit' => 'Edit',
	'details' => 'Details',
	'expand'     => 'Expand',
	'collapse'     => 'Collapse',
	'expand_all'     => 'Expand All',
	'collapse_all'     => 'Collapse All',

	'restore' => 'Restore',
	'disable' => 'Disable',

	'cancel' => 'Cancel',

	'or_alt' => 'or',
	'or' => 'or :action',


	'selection' => array(

		'dropdown' => 'Selection',
		'all' => 'All',
		'clear' => 'Clear',
		'invert' => 'Invert',
        'hint' => 'Select All/Clear selection',

        'at_least_one_document' => 'Select at least 1 document',
        'at_least_one' => 'Please select at least 1 document or collection',
	),
    
    'clipboard' => [
        'copied_title' => 'Copied!',
        'copied_link_text' => 'The link has been copied to your clipboard',
        
        'not_copied_title' => 'Cannot copy to clipboard',
        'not_copied_link_text' => 'The link cannot be copied to the clipboard, you can copy it manually by pressing Ctrl+C on the keyboard.',
    ],


	'switcher' => array(

		'details' => 'Details View',
		'tiles' => 'Tiles View',
		'grid' => 'Grid View',
		'map' => 'Map View',

	),

	'versions' => array(

		'manage' => 'Manage versions',
		'add_new' => 'Add a new version',

	),

	'import' => 'Import',
	'upload' => 'File',
	'upload_alt' => 'Upload',

	'save' => 'Save',
	'saving' => 'Saving...',
	'deleting' => 'Deleting...',
	'restoring' => 'Restoring...',
	'cleaning_trash' => 'Cleaning the trash...',
	'cleaning_trash_wait' => 'Please wait while the trash is being cleaned...',

	'add_to' => 'Add to',
	'move_to' => 'Move to',

	'add_or_move_to' => 'Collections',

	'make_public' => 'Make Public',
	'publish_documents' => 'Publish Documents',
	'make_private' => 'Keep Private',

	'create_add_dropdown' => 'Create or Add',

	'trash_btn' => 'Delete',
	'trash_btn_alt' => 'Move to Trash',
	'restore_btn' => 'Restore',
	'empty_trash' => 'Empty Trash',

	'create_btn' => 'Create',

	'create_collection_btn' => 'Create Collection',
	
	'create_people_group' => 'New Group',
	
	'rename_people_group' => 'Change Group Name',
	
	'delete_people_group' => 'Delete group',
	
	'make_institutional_people_group' => 'Make Institutional',	
	'make_personal_people_group' => 'Keep Personal',
    
    'filters' => array(
        'filter' => 'Filters',
        'clear_filters' => 'Clear Filters',
        'collection_locked' => 'This is the collection you are browsing and cannot be removed.'
    ),
    
    'hints' => [
        'make_public' => 'Select some documents before making them available in the K-Link Network',
    ],
    
    'not_available' => 'Oops, the action is not ready for the primetime!',

    // buttons on the dialog
    'dialogs' => [
        'cancel_btn' => 'No, cancel!',
        'cancel_btn_alt' => 'Cancel',
        
        'yes_btn' => 'Yes',
        'no_btn' => 'No',
        'ok_btn' => 'OK',
        
        'delete_btn' => 'Yes, delete it!',
        
        // showed when on a dialog, like the one in people group creation, the input field is empty, but a value is required
        'input_required' => 'You need to write something!',
        
        
    ]

];
