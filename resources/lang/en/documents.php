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

		'owned_by' => 'owned by',
		'language' => 'language',
		'added_on' => 'added on',
		'last_modified' => 'Last Modified',
		'indexing_error' => 'The document has not been indexed in K-Link',
		'private' => 'Private',
		'shared' => 'Shared',
		'is_public' => 'Public Document',
		'is_public_description' => 'This document is publicly available to other Institution in the K-Link Network',
		'trashed' => 'This document is in the trash',

	),

	'page_title' => 'Documents',

	'menu' => array(
		'all' => 'All',
		'public' => 'Public',
		'private' => 'Private',
		'starred' => 'Starred',
		'shared' => 'Shared',
		'recent' => 'Recent',
		'trash' => 'Trash',
		'not_indexed' => 'Not Indexed',
	),


	'visibility' => array(
		'public' => 'Public',
		'private' => 'Private',
	),

	'type' => array(

		'web-page' => 'web page|web pages',
		'document' => 'document|documents',
		'spreadsheet' => 'spreadsheet|spreadsheets',
		'presentation' => 'presentation|presentations',
		'uri-list' => 'URL list|URLs list',
		'image' => 'image|images',
	),

	'empty_msg' => 'No documents in <strong>:context</strong>',

	'bulk' => array(

		'removed' => ':num element deleted. You can see it in the trash.|:num elements deleted. You can see them in the trash.',
		
		'permanently_removed' => ':num element permanently deleted.|:num elements permanently deleted.',
		
		'restored' => ':num element restored.|:num elements restored.',

		'remove_error' => 'Cannot delete elements. :error',
		
		'copy_error' => 'Cannot copy to collection. :error',
		
		'restore_error' => 'Cannot restore document. :error',
		
		'make_public' => ':num document has been published over the K-Link Public Network|:num documents were made available in the K-Link Network.',
		
		'make_public_error' => 'The publish operation was not completed due to an error. :error',

	),

	'create' => array(
		'page_breadcrumb' => 'Create',
		'page_title' => 'Create a new Document',
	),

	'edit' => array(
		'page_breadcrumb' => 'Edit :document',
		'page_title' => 'Edit :document',

		'title_placeholder' => 'Document Title',

		'abstract_label' => 'Abstract',
		'abstract_placeholder' => 'Document abstract',

		'authors_label' => 'Authors',
		'authors_help' => 'Authors must be specified as a comma separated list of entry formatted like <code>name surname &lt;mail@something.com&gt;</code>',
		'authors_placeholder' => 'Document authors (name surname <mail@something.com>)',

		'language_label' => 'Language',

		'last_edited' => 'Last edit <strong>:time</strong>',
		'created_on' => 'Created on <strong>:time</strong>',
		'uploaded_by' => 'Uploaded by <strong>:name</strong>',

		'public_visibility_description' => 'The document will be made available to all Institution in the K-Link Network',
		
		
		'not_index_message' => 'The document has not yet been succesfully added to K-Link. Please try to <button type="submit">Reindex it</button> now or contact your administrator.',
	),

	'update' => array(
		'error' => 'Cannot Update the document. Nothing has been changed. :error',
	),


	'preview' => array(
		'page_title' => 'Previewing :document',
		'error' => 'Sorry, but we were unable to load the preview of ":document".',
		'not_available' => 'The Document preview cannot be showed for this document.',
		'google_file_disclaimer' => ':document is a Google Drive file, we cannot show the preview here so you have to open it in Google Drive.',
		'open_in_google_drive_btn' => 'Open in Google Drive',
	),

	'versions' => array(

		'section_title' => 'Document Versions',

		'section_title_with_count' => '1 Document version|:number Document versions',

		'version_count_label' => ':number version|:number versions',

		'version_number' => 'version :number',

		'version_current' => 'current',

		'new_version_button' => 'Upload new version',
		
		'new_version_button_uploading' => 'Uploading document...',

		'filealreadyexists' => 'The file version you are uploading already exists in the DMS',
	),

	'messages' => array(
		'updated' => 'Document has been updated',
		'local_public_only' => 'Curretly showing only the Institution\'s Public documents.',
		'forbidden' => 'You don\'t have the ability to make changes to the document.',
		'delete_forbidden' => 'You don\'t have the rights to delete documents, please contact a Content Manager.',
		'delete_public_forbidden' => 'You cannot delete a Public Document, please contact a Quality Content Manager.',
		'delete_force_forbidden' => 'You cannot permanently delete a Document. Please contact a Quality Content Manager.',
		'drag_hint' => 'Drop here the file to start the upload.',
		'recent_hint_dms_manager' => 'You are viewing all the document updates made by each user of the DMS.',
	),
];
