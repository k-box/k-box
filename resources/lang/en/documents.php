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

        'name' => 'Name',
        'added_by' => 'Added by',
        'language' => 'Language',
        'added_on' => 'Added on',
        'last_modified' => 'Last modified',
        'indexing_error' => 'The document has not been indexed in K-Link',
        'private' => 'Private',
        'shared' => 'Shared',
        'is_public' => 'Public Document',
        'is_public_description' => 'This document is publicly available to other Institutions in the K-Link Network',
        'trashed' => 'This document is in Trash. Editing is not possible',
        'klink_public_not_mine' => 'This document is only a reference to the document added to K-Link Public. You cannot make any changes.',
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
        'recent_hint' => 'Here you will find recently modified documents',
        'starred_hint' => 'Here you will find all your starred documents',
    ],

    'sort' => [
        'sorted_by' => 'Sorted by :sort',
        'type_project_name' => 'Project name',
        'type_search_relevance' => 'Search relevance',
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
        'binary' => 'Binary file|Binary files',
    ],

    'empty_msg' => 'No documents in <strong>:context</strong>',
    'empty_msg_recent' => 'No documents for <strong>:range</strong>',

    'bulk' => [

        'removed' => ':num file trashed.|:num files trashed.',
        
        'permanently_removed' => ':num file permanently deleted.|:num files permanently deleted.',
        
        'restored' => ':num file restored.|:num files restored.',

        'remove_error' => 'Cannot delete files. :error',
        
        'copy_error' => 'Cannot copy to collection. :error',
        
        'copy_completed_all' => 'All documents have been added to :collection',
        'copy_completed_some' => '{0}These documents are already in ":collection"|[1,Inf]:count documents added to :collection. :remaining documents are already in :collection',
        
        'restore_error' => 'Cannot restore the document. :error',
        
        // 'make_public' => ':num document has been published over the K-Link Public Network|:num documents were made available in the K-Link Network.',
        
        // 'make_public_error' => 'The publish operation was not completed due to an error. :error',
        // 'make_public_error_title' => 'Cannot publish in K-Link Network',
        
        // 'make_public_success_text_alt' => 'The documents are now publicly available on the K-Link Network',
        // 'make_public_success_title' => 'Publish completed',

        'adding_title' => 'Adding documents...',
        'adding_message' => 'Please wait while the documents are being added to the collection...',
        'added_to_collection' => 'Added',
        'some_added_to_collection' => '{0}Documents not added|[1,Inf]Some documents were not added',
        
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
        'page_title' => 'Create new Document',
    ],

    'edit' => [
        'page_breadcrumb' => 'Edit :document',
        'page_title' => 'Edit :document',

        'title_placeholder' => 'Document Title',

        'abstract_label' => 'Abstract',
        'abstract_placeholder' => 'Document abstract',

        'authors_label' => 'Authors',
        'authors_help' => 'Authors must be specified like <code>name surname &lt;mail@something.com&gt;</code>',
        'authors_placeholder' => 'Document authors (name surname <mail@something.com>)',

        'language_label' => 'Language',

        'last_edited' => 'Last edit <strong>:time</strong>',
        'created_on' => 'Created on <strong>:time</strong>',
        'uploaded_by' => 'Uploaded by <strong>:name</strong>',

        'public_visibility_description' => 'The document will be made available to all Institutions in the K-Link Network',
        
        
        'not_index_message' => 'The document has not yet been succesfully added to K-Link. Please try to <button type="submit">Reindex it</button> now or contact your administrator.',
        'not_fully_uploaded' => 'The upload of this document is still in progress.',
        'preview_available_when_upload_completes' => 'The preview will be available once the upload is completed.',

        'license' => 'License',
        'license_help' => 'License indicates how others can use the work while respecting its copyright terms and conditions.',
        'license_choose_help_button' => 'Help me choose a license',
        
        'copyright_owner' => 'Copyright Owner',
        'copyright_owner_help' => 'Information about the copyright owner is applied independently from the selected license.',
        
        'copyright_owner_name_label' => 'Name',
        'copyright_owner_email_label' => 'E-Mail',
        'copyright_owner_website_label' => 'Website',
        'copyright_owner_address_label' => 'Address',

    ],

    'update' => [
        'error' => 'Cannot Update the document. Nothing has been changed. :error',
        
        'removed_from_title' => 'Removed from collection',
        'removed_from_text' => 'The document has been removed from ":collection"',
        'removed_from_text_alt' => 'The document has been removed from the collection',
        
        'cannot_remove_from_title' => 'Cannot remove from collection',
        'cannot_remove_from_general_error' => 'Cannot remove document from collection. If the problem persists, contact the K-Box Administrator.',

    ],
    
    'restore' => [
        
        'restore_dialog_title' => 'Restore :document?',
        'restore_dialog_text' => 'You are about to restore ":document"',
        'restore_version_dialog_text' => 'You are about to restore the version ":document". This will permanently delete all the newest versions.',
        'restore_dialog_title_count' => 'Restore :count documents?',
        'restore_dialog_text' => 'You are about to restore ":document"',
        'restore_dialog_text_count' => 'You are about to restore :count files',
        'restore_dialog_yes_btn' => 'Restore',
        'restore_dialog_no_btn' => 'Cancel',
        
        'restore_success_title' => 'Restored',
        'restore_error_title' => 'Cannot restore',
        'restore_error_text_generic' => 'The selected file was not moved out of the trash.',
        'restore_version_error_text_generic' => 'The selected previous version could not be restored.',
        'restore_version_error_only_one_version' => 'The document has only one and the latest version.',
      
        'restoring' => 'Restoring...',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Trash ":document"?',
        'dialog_title_alt' => 'Trash document?',
        'dialog_title_count' => 'Move :count documents to Trash?',
        'dialog_text' => 'You are about to move :document to Trash.',
        'dialog_text_count' => 'You are about to move :count documents to Trash',
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
        'dialog_text' => 'You are about to permanently delete :document. This operation cannot be undone.',
        'dialog_text_count' => 'You are about to permanently delete :count documents. This operation cannot be undone.',
        'deleted_dialog_title' => ':document has been permanently deleted',
        'deleted_dialog_title_alt' => 'Permanently Deleted',
        'cannot_delete_dialog_title' => 'Cannot delete ":document" permanently',
        'cannot_delete_dialog_title_alt' => 'Cannot delete permanently',
        'cannot_delete_general_error' => 'There was a problem while permanently deleting the document. Please contact the K-Box Administrator.',
    ],

    'preview' => [
        'page_title' => 'Previewing :document',
        'error' => 'Sorry, we were unable to load the preview of ":document".',
        'not_available' => 'The preview cannot be showed for this document.',
        'not_supported' => 'A preview cannot be offered for this file. The file format is currently not supported.',
        'google_file_disclaimer' => ':document is a Google Drive file. We cannot show the preview here. You have to open it in Google Drive.',
        'google_file_disclaimer_alt' => 'This is a Google Drive file and cannot be previewed here.',
        'open_in_google_drive_btn' => 'Open in Google Drive',
        'video_not_ready' => 'The video is being processed. It will be available within seconds.',
        'file_not_ready' => 'The file is being processed by the K-Box. During file processing the preview is not available, please check back later.',
    ],

    'versions' => [

        'section_title' => 'Versions',

        'section_title_with_count' => '1 version|:number versions',

        'version_count_label' => ':number version|:number versions',

        'version_number' => 'version :number',

        'version_current' => 'current',

        'new_version_button' => 'Upload new version',
        
        'new_version_button_uploading' => 'Uploading...',

        'filealreadyexists' => 'The file version already exists in the K-Box',
    ],

    'messages' => [
        'updated' => 'Document details changed. Processing the changes... The document might not be available in search results yet.',
        'processing' => 'The document is being processed by the K-Box. It might not be immediately available in search results.',
        'local_public_only' => 'Currently showing only the Institution\'s Public documents.',
        'forbidden' => 'You do not have the ability to make changes to the document.',
        'delete_forbidden' => 'You do not have the permissions to delete documents. Please contact the Project Manager or Administrator.',
        'delete_public_forbidden' => 'You cannot delete a public document. Please contact the Project Manager or Administrator.',
        'delete_force_forbidden' => 'You cannot permanently delete a document. Please contact the Project Manager or Administrator.',
        'drag_hint' => 'Drop the file to start the upload.',
        'recent_hint_dms_manager' => 'You are viewing document updates performed by all K-Box users in accessible projects.',
        'no_documents' => 'No documents. Upload new documents using the "Create or Add" button above or by dragging and dropping them here.',
    ],
    
    
    'trash' => [
        
        'clean_title' => 'Clean trash?',
        'yes_btn' => 'Clean',
        'no_btn' => 'Cancel',

        'empty_trash' => 'Nothing in Trash',
        
        'empty_all_text' => 'All the documents in the Trash will be permanently deleted. This action will remove files and revision, starred, collections and shares. This action cannot be undone.',
        'empty_selected_text' => 'You are about to permanently delete the selected documents. You will remove also files and revision, starred, collections and shares. This action cannot be undone.',
        
        'cleaned' => 'Trash Cleaned',
        'cannot_clean' => 'Cannot clean Trash',
        'cannot_clean_general_error' => 'There was a problem cleaning the Trash. Please contact an Administrator, if the problem persists.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Your browser does not support folder drag and drop.',
        'error_dialog_title' => 'File Upload error',
        
        'max_uploads_reached_title' => 'Sorry, you have to wait a little',
        'max_uploads_reached_text' => 'We can process only a small number of files. Please have a little patience before adding another file.',
        
        'all_uploaded' => 'All the files have been successfully uploaded.',
        
        'upload_dialog_title' => 'Upload',
        'page_title' => 'Upload',
        'dragdrop_not_supported' => 'Your browser does not support drag and drop file uploads.',
        'dragdrop_not_supported_text' => 'Please upload your files using "Create or Add".',
        'remove_btn' => "Remove file", //this is the little link that is showed after the file upload has been processed
        'cancel_btn' => 'Cancel upload', //for future use
        'cancel_question' => 'Are you sure you want to cancel this upload?',  //for future use
        'outside_project_target_area' => 'Please drag and drop your file over a Project to upload it.',
        'empty_file_error' => 'Empty file. Please upload a file, which has at least one word in it.',
    ],

    'duplicates' => [
        'badge' => 'This is a duplicate of an existing document',
        'duplicates_btn' => 'Duplicates',
        'duplicates_btn_hint' => 'See and manage duplicates',
        'duplicates_description' => 'This document is a possible duplicate of',

        'in_trash' => 'in Trash',

        'message_me_owner' => 'The document you uploaded as <a href=":duplicate_link" target="_blank" rel="noopener noreferrer">:duplicate_title</a> is a duplicate of your document <a href=":existing_link" target="_blank" rel="noopener noreferrer">:existing_title</a>.',
        'message_with_owner' => 'The document you uploaded as <a href=":duplicate_link" target="_blank" rel="noopener noreferrer">:duplicate_title</a> is a duplicate of <a href=":existing_link" target="_blank" rel="noopener noreferrer">:existing_title</a> (uploaded by :owner).',
        'message_in_collection' => 'The document you uploaded as <a href=":duplicate_link" target="_blank" rel="noopener noreferrer">:duplicate_title</a> is a duplicate of <a href=":existing_link" target="_blank" rel="noopener noreferrer">:existing_title</a> (uploaded by :owner in :collections).',
        
        'resolve_duplicate_button' => 'Resolve duplicate using this, already existing, document',

        'processing' => 'Resolving duplicate using existing document...',

        'errors' => [
            'title' => 'Duplicate resolution not completed',
            'generic' => 'There was a problem on our end and the duplicate was not resolved',
            'already_resolved' => 'The conflict was already resolved',
            'resolve_with_trashed_document' => 'The conflict was already resolved',
        ],
    ],
];
