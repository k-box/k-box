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

    'upload' => [
        'simple' => 'Upload error :description',
        'no_file_sent' => 'The file was not sent',
        'filenamepolicy' => 'The file :filename does not respect the naming convention.',
        'filealreadyexists' => 'The file :filename already exists.',
        'file_not_uploaded' => 'The file was not uploaded, please verify that its size is less than :max_size.',
        
    ],

    'filealreadyexists' => [
        'generic' => 'File :name already exists in the K-Box with the title <strong>":title"</strong>.',
        'incollection' => 'File is already available in <a href=":collection_link"><strong>":collection"</strong></a> with the title <strong>":title"</strong>',
        'incollection_by_you' => 'You have already uploaded this file as <strong>":title"</strong> in <a href=":collection_link"><strong>":collection"</strong></a>',
        'by_you' => 'You have already uploaded this file as <strong>":title"</strong>',
        'revision_of_document' => 'File you are uploading is an existing revision of <strong>":title"</strong>, added by :user (:email)',
        'revision_of_your_document' => 'This file is an existing revision of <strong>:title</strong>',
        'by_user' => 'File has already been added to the K-Box by :user (:email).',
        'in_the_network' => 'File is already available in <strong>:network</strong> as <strong>":title"</strong>. Added by :institution',
    ],

    'group_already_exists_exception' => [
        'only_name' => 'Collection named ":name" already exists.',
        'name_and_parent' => 'Collection ":name" already exists under ":parent".',
    ],
    
    'generic_text' => 'Something unexpected has happened.',
    'generic_text_alt' => 'Something unexpected has happened. :error',
    'generic_title' => 'Sorry',

    'reindex_all' => 'The "reindex all" procedure cannot be completed due to an error. See the logs or contact the administrator.',

    'token_mismatch_exception' => 'Your session has expired. Please refresh the page to continue with your work.',

    'not_found' => 'The resource you are looking for cannot be found.',
    
    'document_not_found' => 'The file you are looking for cannot be found or was deleted.',

    'forbidden_exception' => 'You do not have access to the page.', //was thrown when Clean_Trash was requested
    'forbidden_edit_document_exception' => 'You cannot edit the file.',
    'forbidden_see_document_exception' => 'You cannot view personal file of another user.',
    
    'kcore_connection_problem' => 'The connection to the K-Link Core cannot be established.',

    'fatal' => 'Fatal error :reason',

    'panels' => [
        'title' => 'Something unexpected has happened.',
        'prevent_edit' => 'You cannot edit :name',
    ],


    'group_edit_institution' => "You cannot edit institution level groups.",
    'group_edit_project' => "You cannot edit project collections.",
    'group_edit_else' => "You cannot edit someone else's groups.",

    '503_title' => 'K-Box Maintenance',
    '503_text' => 'The <strong>K-Box</strong> is currently in<br/><strong>maintenance.</strong><br/><small> Will be back shortly :)</small>',
    
    '503-readonly_title' => 'K-Box is Readonly',
    '503-readonly_text_styled' => 'The <strong>K-Box</strong> is currently in <strong>readonly mode.</strong><br/><small> For maintenance reasons you cannot change or upload content.</small>',
    '503-readonly_text' => 'The K-Box is currently in readonly mode. For maintenance reasons you cannot change or upload content.',

    '500_title' => 'Error - K-Box',
    '500_text' => 'Something <strong>bad</strong><br/>and unexpected <strong>has happened</strong>. <br/>We are deeply sorry.',

    '404_title' => 'Not Found on the K-Box',
    '404_text' => '<strong>The page</strong><br/>you are looking for<br/><strong>does not exist</strong> anymore.',
    
    '401_title' => 'You cannot view the page - K-Box',
    '401_text' => 'You <strong>cannot</strong> view the page.<br/> Please acquire the <strong>access</strong> permissions first.',
    
    'login_title' => 'Please login - K-Box',
    'login_text' => 'You need to be logged-in to view the document.',
    
    '403_title' => 'You do not have the permission to view the page',
    '403_text' => 'You <strong>cannot</strong> view the page.<br/> Please acquire the <strong>access</strong> permissions first.',

    '405_title' => 'Method Not Allowed on the K-Box',
    '405_text' => 'Do not call me like this again.',
    
    '413_title' => 'Document Excessive File Size',
    '413_text' => 'Your upload exceeds the maximum allowed file size.',
    
    'klink_exception_title' => 'K-Link Services Error',
    'klink_exception_text' => 'There was a problem connecting to the K-Link Services.',
    
    'reindex_failed' => 'Search might not be up-to-date with latest changes. Please consult the support team for more information.',
    
    'page_loading_title' => 'Loading Problem',
    'page_loading_text' => 'Page loading seems slow and some functionality may not be available. Please refresh the page.',
    
    'dragdrop' => [
        'not_permitted_title' => 'Drag and Drop not Available',
        'not_permitted_text' => 'Drag and Drop is not possible here.',
        'link_not_permitted_title' => 'Drag links is not available',
        'link_not_permitted_text' => 'Currently you cannot drag and drop links to websites.',
    ],

    'support_widget_opened_for_you' => 'We have opened the support widget for you. Please drop us a line so we can investigate on the error. Thanks for your contribution.',
    'go_back_btn' => 'Leave this page',
    
    'preference_not_saved_title' => 'Preference not saved',
    'preference_not_saved_text' => 'We were unable to save your preference. Please try again later.',

    'generic_form_error' => 'You have some errors. Please correct them before continuing.',

    'oldbrowser' => [
        'generic' => 'Your browser is out of date. For a better experience, keep your browser up to date.',
        'ie8' => 'Your browser (Internet Explorer 8) is out of date. It has known security flaws and cannot display all features of K-Link. For a better experience, keep your browser up to date.',
        'nosupport' => 'Your browser version is not supported by the K-Box.',
        
        'more_info' => 'More information',
        'dismiss' => 'Dismiss',
    ],

    'quota' => [
        'exceeded' => 'You have exceeded the storage quota assigned',
        'not_enough_free_space' => 'Not enough free space available (:free) in your assigned quota (:quota).',
    ],

];
