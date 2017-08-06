<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Networks Language Lines
    |--------------------------------------------------------------------------
    |
    | contains messages for localizing actions on different public networks
    |
    | original strings taken from
    | - actions.make_public
    | - actions.publish_documents
    | - documents.bulk.making_public_title
    | - documents.bulk.making_public_text
    | - documents.bulk.make_public_error
    | - documents.bulk.make_public_error_title
    | - documents.bulk.make_public_success_text_alt
    | - documents.bulk.make_public_success_title
    | - documents.bulk.make_public_change_title_not_available
    | - documents.bulk.make_public_all_collection_dialog_text
    | - documents.bulk.make_public_inside_collection_dialog_text
    | - documents.bulk.make_public_dialog_title
    | - documents.bulk.make_public_dialog_title_alt
    | - documents.bulk.publish_btn
    | - documents.bulk.make_public_empty_selection
    | - documents.bulk.make_public_dialog_text
    | - documents.bulk.make_public_dialog_text_count
    |
    |
    */

    'klink_network_name' => 'K-Link Public network',
    'menu_public_klink' => 'K-Link Public',
    'menu_public' => ':network',
    'menu_public_hint' => 'Explore the documents available in the :network',

    'make_public' => 'Make Public',
    'publish_to_short' => 'Publish',
    'publish_to_long' => 'Publish to :network',

    
    'publish_to_hint' => 'Select some documents to be published on the :network',
    

    'publish_btn' => 'Publish!',

    'settings' => [
        'section' => 'Join a network',
        'section_help' => 'Here you can configure how the DMS joins a network',
        'enabled' => 'Enable publish documents to the network',
        'debug_enabled' => 'Enable the Debug of the network connection',
        'username' => 'The user used for authenticating with the Network',
        'password' => 'The password used for authenticating with the Network',
        'url' => 'The URL of the Network Entry Point',
        'name_en' => 'Network name (english version)',
        'name_ru' => 'Network name (russian version)',
        'name_section' => 'Network name',
        'name_section_help' => 'Give the network a name, this will be used on the UI when publishing documents or collections. With both fields empty the "K-Link Public Network" name will be used',
    ],

    'made_public' => ':num document has been published over the :network|:num documents were made available in the :network.',
        
    'make_public_error' => 'The publish operation was not completed due to an error. :error',
    'make_public_error_title' => 'Cannot publish in :network',
    
    'make_public_success_text_alt' => 'The documents are now available on the :network',
    'make_public_success_title' => 'Publish completed',

    'making_public_title' => 'Publishing on :network...',
    'making_public_text' => 'Please wait while the documents will be made available in the :network',

    'make_public_change_title_not_available' => 'The option for changing title before Publish is not currently available.',

    'make_public_all_collection_dialog_text' => 'You will make all the documents in this collection available on the :network. (click outside to undo)',
    'make_public_inside_collection_dialog_text' => 'You will make all the documents inside ":item" available on the :network. (click outside to undo)',
    
    'make_public_dialog_title' => 'Publish ":item" on :network',
    'make_public_dialog_title_alt' => 'Publish on :network',
    
    
    'make_public_empty_selection' => 'Please select the documents you want to make available in the :network.',
    
    'make_public_dialog_text' => 'You will make ":item" available on the :network. (click outside to stop)',
    'make_public_dialog_text_count' => 'You will make :count documents available on the :network. (click outside to stop)',

];
