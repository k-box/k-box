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

        'name' => 'nom',
        'added_by' => 'ajouté par',
        'language' => 'langue',
        'added_on' => 'ajouté le',
        'last_modified' => 'dernière modification',
        'indexing_error' => 'Ca document n\'a pas été indexé par K-Link',
        'private' => 'Privé',
        'shared' => 'Partagé',
        'is_public' => 'Document Public',
        'is_public_description' => 'Ce document est publiquement accessible par toutes les institutions membres du réseau K-Link',
        'trashed' => 'Ce document est dans la corbeille',
        'klink_public_not_mine' => 'Ce document est uniquement une référence à un document ajouté à K-Link Public et vous ne pouvez donc pas le modifier.',
    ],

    'page_title' => 'Documents',

    'menu' => [
        'all' => 'Tous',
        'public' => 'K-Link Public',
        'private' => 'Privé',
        'personal' => 'Personel',
        'starred' => 'Favori',
        'shared' => 'Partagés avec moi',
        'recent' => 'Récents',
        'trash' => 'Corbeille',
        'not_indexed' => 'Pas indexé',
        'recent_hint' => 'Vous trouverez ici les documents récemment modifiés que vous possédez',
        'starred_hint' => 'You will find here all your starred documents',
    ],

    'sort' => [
        'sorted_by' => 'Ordonner par :sort',
        'type_project_name' => 'nom du projet',
        'type_search_relevance' => 'pertinence',
        'type_updated_at' => 'date de mise à jour',
    ],

    'filtering' => [
        'date_range_hint' => 'Période de temps préférée',
        'items_per_page_hint' => 'Nombre d\'objets par page',
        'today' => 'Aujourd\'hui',
        'yesterday' => 'Depuis hier',
        'currentweek' => 'Derniers 7 jours',
        'currentmonth' => 'Derniers 30 jours',
    ],

    'visibility' => [
        'public' => 'Public',
        'private' => 'Privé',
    ],

    'type' => [

        'web-page' => 'page web|pages web',
        'document' => 'document|documents',
        'spreadsheet' => 'tableur|tableurs',
        'presentation' => 'présentation|présentations',
        'uri-list' => 'liste d\'URLs|listes d\'URLs',
        'image' => 'image|images',
        'geodata' => 'jeu de données géographiques|jeux de données géographiques',
        'text-document' => 'document texte|documents texte',
        'video' => 'video|videos',
        'archive' => 'archive|archives',
        'PDF' => 'PDF|PDFs',
    ],

    'empty_msg' => 'Aucun document dans <strong>:context</strong>',
    'empty_msg_recent' => 'Aucun document pour <strong>:range</strong>',

    'bulk' => [

        'removed' => ':num fichier effacé.|:num fichiers effacés.',
        
        'permanently_removed' => ':num fichier effacé définitivement.|:num fichiers effacés définitivement.',
        
        'restored' => ':num fichier restauré.|:num fichiers restaurés.',

        'remove_error' => 'Impossible d\'effacet les fichiers. :error',
        
        'copy_error' => 'Impossible de copier la collection. :error',
        
        'copy_completed_all' => 'Tous les documents ont ét´ajoutés à :collection',
        'copy_completed_some' => '{0}Aucun document n\'a été ajouté car il existe déjà dans ":collection"|[1,Inf]:count Documents ajoutés à :collection, les autres :remaining existaient déjà dans :collection',
        
        'restore_error' => 'Impossible de restaurer le document. :error',
        
        // 'make_public' => ':num document has been published over the K-Link Public Network|:num documents were made available in the K-Link Network.',
        
        // 'make_public_error' => 'The publish operation was not completed due to an error. :error',
        // 'make_public_error_title' => 'Cannot publish in K-Link Network',
        
        // 'make_public_success_text_alt' => 'The documents are now publicly available on the K-Link Network',
        // 'make_public_success_title' => 'Publish completed',

        'adding_title' => 'Ajout des documents...',
        'adding_message' => 'Veuillez patienter pendant que les documents sont ajoutés à la collection...',
        'added_to_collection' => 'Ajouté',
        'some_added_to_collection' => '{0}Documents non ajoutés|[1,Inf]Certains documents non ajoutés',
        
        'add_to_error' => 'Impossible d\'ajouter à la collection',
        
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
        'page_breadcrumb' => 'Créer',
        'page_title' => 'Créer un nouveau document',
    ],

    'edit' => [
        'page_breadcrumb' => 'Editer :document',
        'page_title' => 'Editer :document',

        'title_placeholder' => 'Titre du document',

        'abstract_label' => 'Résumé',
        'abstract_placeholder' => 'Résumé du document',

        'authors_label' => 'Auteurs',
        'authors_help' => '´Donnez le nom de chaque auteur, séparé par une virgule, comme <code>name surname &lt;mail@something.com&gt;</code>',
        'authors_placeholder' => 'Auteurs du document authors (prénom nom <mail@something.com>)',

        'language_label' => 'Langue',

        'last_edited' => 'Dernière modification <strong>:time</strong>',
        'created_on' => 'Crée le <strong>:time</strong>',
        'uploaded_by' => 'Mis en ligne par <strong>:name</strong>',

        'public_visibility_description' => 'Ce document sera accessible par toutes les institutions membres du réseau K-Link',
        
        
        'not_index_message' => 'Ce document n\'a pas encore pu être ajouté à K-Link.  Veuilles essayer de <button type="submit">le ré-indexer</button> maintenant ou contactez votre administrateur.',
        'not_fully_uploaded' => 'La mise en ligne de ce document est encore en cours.',
        'preview_available_when_upload_completes' => 'L\'aperçu sera disponible quand la mise en ligne sera terminée.',
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
        'video_not_ready' => 'The video is being processed. It will be available within seconds.',
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
