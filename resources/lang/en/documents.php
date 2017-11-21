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

    'descriptor' => [

        'name' => 'name',
        'added_by' => 'added by',
        'language' => 'language',
        'added_on' => 'added on',
        'last_modified' => 'last modified',
        'indexing_error' => 'The document has not been indexed in K-Link',
        'private' => 'Private',
        'shared' => 'Shared',
        'is_public' => 'Public Document',
        'is_public_description' => 'This document is publicly available to other Institution in the K-Link Network',
        'trashed' => 'This document is in the trash',
        'klink_public_not_mine' => 'This document is only a reference to the document added to K-Link Public, therefore you cannot make any changes.',
    ],

    'page_title' => 'Documents',

    'menu' => [
        'all' => 'All',
        'public' => 'K-Link Public',
        'private' => 'Private',
        'personal' => 'Personal',
        'starred' => 'Starred',
        'shared' => 'Shared with me',
        'recent' => 'Recent',
        'trash' => 'Trash',
        'not_indexed' => 'Not Indexed',
        'recent_hint' => 'You will find here recently modified documents you own',
        'starred_hint' => 'You will find here all your starred documents',
    ],

    'sort' => [
        'sorted_by' => 'Sorted by :sort',
        'type_project_name' => 'project name',
        'type_search_relevance' => 'search relevance',
        'type_updated_at' => 'update date',
    ],

    'filtering' => [
        'date_range_hint' => 'Preferred time range',
        'items_per_page_hint' => 'Number of items per page',
        'today' => 'Today',
        'yesterday' => 'Since Yesterday',
        'currentweek' => 'Last 7 days',
        'currentmonth' => 'Last 30 days',
    ],

    'visibility' => [
        'public' => 'Public',
        'private' => 'Private',
    ],

    'type' => [

        'web-page' => 'web page|web pages',
        'document' => 'document|documents',
        'spreadsheet' => 'spreadsheet|spreadsheets',
        'presentation' => 'presentation|presentations',
        'uri-list' => 'URL list|URLs list',
        'image' => 'image|images',
        'geodata' => 'geographic data|geographic data',
        'text-document' => 'textual document|textual documents',
        'video' => 'video|videos',
        'archive' => 'archive|archives',
        'PDF' => 'PDF|PDFs',
    ],

    'empty_msg' => 'No documents in <strong>:context</strong>',
    'empty_msg_recent' => 'No documents for <strong>:range</strong>',

    'bulk' => [

        'removed' => ':num file deleted.|:num files deleted.',
        
        'permanently_removed' => ':num file permanently deleted.|:num files permanently deleted.',
        
        'restored' => ':num file restored.|:num files restored.',

        'remove_error' => 'Cannot delete files. :error',
        
        'copy_error' => 'Cannot copy to collection. :error',
        
        'copy_completed_all' => 'All documents has been added to :collection',
        'copy_completed_some' => '{0}No documents has been added because were already in ":collection"|[1,Inf]:count Documents added to :collection, the remaining :remaining where already in :collection',
        
        'restore_error' => 'Cannot restore document. :error',
        
        // 'make_public' => ':num document has been published over the K-Link Public Network|:num documents were made available in the K-Link Network.',
        
        // 'make_public_error' => 'The publish operation was not completed due to an error. :error',
        // 'make_public_error_title' => 'Cannot publish in K-Link Network',
        
        // 'make_public_success_text_alt' => 'The documents are now publicly available on the K-Link Network',
        // 'make_public_success_title' => 'Publish completed',

        'adding_title' => 'Adding documents...',
        'adding_message' => 'Please wait while the documents are being added to the collection...',
        'added_to_collection' => 'Added',
        'some_added_to_collection' => '{0}Documents not added|[1,Inf]Some documents not added',
        
        'add_to_error' => 'Cannot add to collection',
        
        // 'making_public_title' => 'Publishing...',
        // 'making_public_text' => 'Please wait while the documents will be made publicly available in the K-Link Network',
    
        // 'make_public_change_title_not_available' => 'The option for changing title before Publish is not currently available.',

        // 'make_public_all_collection_dialog_text' => 'You will make all the documents in this collection publicly available on the K-Link Network. (click outside to undo)',
        // 'make_public_inside_collection_dialog_text' => 'You will make all the documents inside ":item" publicly available on the K-Link Network. (click outside to undo)',
        
        // 'make_public_dialog_title' => 'Publish ":item" on K-Link Network',
        // 'make_public_dialog_title_alt' => 'Publish on K-Link Network',
        
        // 'publish_btn' => 'Publish!',
        // 'make_public_empty_selection' => 'Please select the documents you want to make available in the K-Link Network.',
        
        // 'make_public_dialog_text' => 'You will make ":item" publicly available on the K-Link Network. (click outside to stop)',
        // 'make_public_dialog_text_count' => 'You will make :count documents publicly available on the K-Link Network. (click outside to stop)',
    ],

    'create' => [
        'page_breadcrumb' => 'Create',
        'page_title' => 'Create a new Document',
    ],

    'edit' => [
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
        'not_fully_uploaded' => 'The upload of this document is still in progress.',
        'preview_available_when_upload_completes' => 'The preview will be available once the upload completes.',
    ],

    'update' => [
        'error' => 'Cannot Update the document. Nothing has been changed. :error',
        
        'removed_from_title' => 'Removed from collection',
        'removed_from_text' => 'The document has been removed from ":collection"',
        'removed_from_text_alt' => 'The document has been removed from the collection',
        
        'cannot_remove_from_title' => 'Cannot remove from collection',
        'cannot_remove_from_general_error' => 'Cannot remove document from collection, if the problem persists please contact the DMS Administrator.',

    ],
    
    'restore' => [
        
        'restore_dialog_title' => 'Restore :document?',
        'restore_dialog_text' => 'You\'re about to restore ":document"',
        'restore_dialog_title_count' => 'Restore :count documents?',
        'restore_dialog_text' => 'You\'re about to restore ":document"',
        'restore_dialog_text_count' => 'You\'re about to restore :count files',
        'restore_dialog_yes_btn' => 'Yes, restore',
        'restore_dialog_no_btn' => 'No',
        
        'restore_success_title' => 'Restored',
        'restore_error_title' => 'Cannot restore',
        'restore_error_text_generic' => 'The selected file was not moved out of the trash.',
      
        'restoring' => 'Restoring...',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Trash ":document"?',
        'dialog_title_alt' => 'Trash document?',
        'dialog_title_count' => 'Move :count documents to Trash?',
        'dialog_text' => 'You\'re about to move :document to Trash.',
        'dialog_text_count' => 'You\'re about to move :count documents to Trash',
        'deleted_dialog_title' => ':document has been trashed',
        'deleted_dialog_title_alt' => 'Trashed',
        'cannot_delete_dialog_title' => 'Cannot trash ":document"',
        'cannot_delete_dialog_title_alt' => 'Cannot trash',
        'cannot_delete_general_error' => 'There was a problem moving the document to Trash, please contact an Administrator.',
    ],

    'permanent_delete' => [
        
        'dialog_title' => 'Permanently Delete ":document"?',
        'dialog_title_alt' => 'Permanently Delete document?',
        'dialog_title_count' => 'Delete :count documents?',
        'dialog_text' => 'You\'re about to permanently delete :document. This operation cannot be undone.',
        'dialog_text_count' => 'You\'re about to permanently delete :count documents. This operation cannot be undone.',
        'deleted_dialog_title' => ':document has been permanently deleted',
        'deleted_dialog_title_alt' => 'Permanently Deleted',
        'cannot_delete_dialog_title' => 'Cannot permanently delete ":document"!',
        'cannot_delete_dialog_title_alt' => 'Cannot permanently delete!',
        'cannot_delete_general_error' => 'There was a problem while permanently deleting the document, please contact an Administrator.',
    ],

    'preview' => [
        'page_title' => 'Previewing :document',
        'error' => 'Sorry, but we were unable to load the preview of ":document".',
        'not_available' => 'The document preview cannot be showed for this document.',
        'google_file_disclaimer' => ':document is a Google Drive file, we cannot show the preview here. You have to open it in Google Drive.',
        'google_file_disclaimer_alt' => 'This is a Google Drive file and cannot be previewed here.',
        'open_in_google_drive_btn' => 'Open in Google Drive',
    ],

    'versions' => [

        'section_title' => 'Versions',

        'section_title_with_count' => '1 version|:number versions',

        'version_count_label' => ':number version|:number versions',

        'version_number' => 'version :number',

        'version_current' => 'current',

        'new_version_button' => 'Upload new version',
        
        'new_version_button_uploading' => 'Uploading...',

        'filealreadyexists' => 'The file version you are uploading already exists in the DMS',
    ],

    'messages' => [
        'updated' => 'Document details changed. Processing the changes, the document might not be available in search results yet.',
        'processing' => 'The document is being processed by the K-Box. It might not be immediately available in search results.',
        'local_public_only' => 'Currently showing only the Institution\'s Public documents.',
        'forbidden' => 'You don\'t have the ability to make changes to the document.',
        'delete_forbidden' => 'You don\'t have the rights to delete documents, please contact a Project Manager or Administrator.',
        'delete_public_forbidden' => 'You cannot delete a Public Document, please contact a K-Linker or Administrator.',
        'delete_force_forbidden' => 'You cannot permanently delete a Document. Please contact a Project Manager or Administrator.',
        'drag_hint' => 'Drop the file to start the upload.',
        'recent_hint_dms_manager' => 'You are viewing all the document updates made by each user of the K-Box.',
        'no_documents' => 'No Documents, you can upload new documents here using the "Create or Add" button above or by dragging and dropping them here.',
    ],
    
    
    'trash' => [
        
        'clean_title' => 'Clean trash?',
        'yes_btn' => 'Yes, clean',
        'no_btn' => 'No',

        'empty_trash' => 'Nothing in trash',
        
        'empty_all_text' => 'All the documents in the trash will be permanently deleted. This action will remove files and revision, starred, collections and shares. This action cannot be undone.',
        'empty_selected_text' => 'You\'re about to permanently delete the selected documents. You will remove also files and revision, starred, collections and shares. This action cannot be undone.',
        
        'cleaned' => 'Trash Cleaned',
        'cannot_clean' => 'Cannot clean trash',
        'cannot_clean_general_error' => 'There was a problem cleaning the trash, please contact an Administrator if the problem persists.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Your browser don\'t support folder drag and drop.',
        'error_dialog_title' => 'File Upload error',
        
        'max_uploads_reached_title' => 'Sorry, but you have to wait a little',
        'max_uploads_reached_text' => 'We can process only a little amount of file, so please have a little patience before adding another file.',
        
        'all_uploaded' => 'All the files have been successfully uploaded.',
        
        'upload_dialog_title' => 'Upload',
        'page_title' => 'Upload',
        'dragdrop_not_supported' => 'Your browser does not support drag and drop file uploads.',
        'dragdrop_not_supported_text' => 'Please upload your files using file selector in "Create or Add".',
        'remove_btn' => "Remove file", //this is the little link that is showed after the file upload has been processed
        'cancel_btn' => 'Cancel upload', //for future use
        'cancel_question' => 'Are you sure you want to cancel this upload?',  //for future use
        'outside_project_target_area' => 'Please drag and drop your file over a Project to upload it.',
        'empty_file_error' => 'Empty file, please upload a file, which has at least one word in it.',
    ],
];
