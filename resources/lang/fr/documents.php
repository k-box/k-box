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
        'personal' => 'My Uploads',
        'starred' => 'Favoris',
        'shared' => 'Partagés avec moi',
        'recent' => 'Récents',
        'trash' => 'Corbeille',
        'not_indexed' => 'Pas indexé',
        'recent_hint' => 'Vous trouverez ici les documents récemment mis en ligne ou modifiés',
        'starred_hint' => 'Vous trouverez ici tous vos documents favoris',
    ],

    'sort' => [
        'sorted_by' => 'Trier par :sort',
        'type_project_name' => 'nom du projet',
        'type_search_relevance' => 'pertinence',
        'type_updated_at' => 'date de mise à jour',
    ],

    'filtering' => [
        'date_range_hint' => 'Fichiers mis en ligne depuis...',
        'items_per_page_hint' => 'Nombre de fichiers par page',
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

        'remove_error' => 'Impossible d\'effacer les fichiers. :error',
        
        'copy_error' => 'Impossible de copier la collection. :error',
        
        'copy_completed_all' => 'Tous les documents ont été ajoutés à :collection',
        'copy_completed_some' => '{0}Aucun document n\'a été ajouté car il existe déjà dans ":collection"|[1,Inf]:count Documents ajoutés à :collection, les autres :remaining existaient déjà dans :collection',
        
        'restore_error' => 'Impossible de restaurer le document. :error',

        'adding_title' => 'Ajout des documents...',
        'adding_message' => 'Veuillez patienter pendant que les documents sont ajoutés à la collection...',
        'added_to_collection' => 'Ajouté',
        'some_added_to_collection' => '{0}Document non ajouté|[1,Inf]Certains documents non ajoutés',
        
        'add_to_error' => 'Impossible d\'ajouter à la collection',
        
    ],

    'create' => [
        'page_breadcrumb' => 'Créer',
        'page_title' => 'Créer un nouveau document',
    ],

    'edit' => [
        'page_breadcrumb' => 'Modifier :document',
        'page_title' => 'Modifier :document',

        'title_placeholder' => 'Titre du document',

        'abstract_label' => 'Résumé',
        'abstract_placeholder' => 'Résumé du document',

        'authors_label' => 'Auteurs',
        'authors_help' => 'Donnez le nom de chaque auteur, séparé par une virgule, comme <code>prénom surname &lt;mail@something.com&gt;</code>',
        'authors_placeholder' => 'Auteurs du document (prénom nom <mail@something.com>)',

        'language_label' => 'Langue',

        'last_edited' => 'Dernière modification <strong>:time</strong>',
        'created_on' => 'Créé le <strong>:time</strong>',
        'uploaded_by' => 'Mis en ligne par <strong>:name</strong>',

        'public_visibility_description' => 'Ce document sera accessible par toutes les institutions membres du réseau K-Link',
        
        
        'not_index_message' => 'Ce document n\'a pas encore pu être ajouté à K-Link. Veuillez essayer de <button type="submit">le ré-indexer</button> maintenant ou contactez votre administrateur.',
        'not_fully_uploaded' => 'La mise en ligne de ce document est encore en cours.',
        'preview_available_when_upload_completes' => 'L\'aperçu sera disponible quand la mise en ligne sera terminée.',
        
        'license' => 'Licence',
        'license_help' => 'Les licences permettent au détenteur des droits d\'auteurs d\'un document d\'accorder à quiconque l\'autorisation de l\'utiliser.',
        'license_choose_help_button' => 'Aidez moi à choisir une licence',
        
        'copyright_owner' => 'Droits d\'auteurs',
        'copyright_owner_help' => 'Information sur le détenteur des droits d\'auteurs. Cette information est valable indépendamment de la licence choisie',
        
        'copyright_owner_name_label' => 'Nom',
        'copyright_owner_email_label' => 'Email',
        'copyright_owner_website_label' => 'Site web',
        'copyright_owner_address_label' => 'Addresse',
    ],

    'update' => [
        'error' => 'Impossible de mettre à jour le document. Rien n\'a changé. :error',
        
        'removed_from_title' => 'Supprimé de la collection',
        'removed_from_text' => 'Ce document a été supprimé de ":collection"',
        'removed_from_text_alt' => 'Ce document a été supprimé de la collection',
        
        'cannot_remove_from_title' => 'Impossible de supprimer de la collection',
        'cannot_remove_from_general_error' => 'Impossible de supprimer le document de la collection. Si le problème se répète, veuillez contacter votre administrateur.',

    ],
    
    'restore' => [
        
        'restore_dialog_title' => 'Restaurer :document?',
        'restore_dialog_text' => 'Vous allez restaurer ":document"',
        'restore_version_dialog_text' => 'Vous êtes sur le point de restaurer la version ":document". Ceci va effacer de façon définitive toutes les versions plus récentes.',
        'restore_dialog_title_count' => 'Restaurer :count documents?',
        'restore_dialog_text' => 'Vous allez restaurer ":document"',
        'restore_dialog_text_count' => 'Vous allez restaurer :count fichiers',
        'restore_dialog_yes_btn' => 'Oui, restaurer',
        'restore_dialog_no_btn' => 'Non',
        
        'restore_success_title' => 'Restauré',
        'restore_error_title' => 'Impossible de restaurer',
        'restore_error_text_generic' => 'Le fichier sélectionné n\'a pas été sorti de la corbeille.',
        'restore_version_error_text_generic' => 'Impossible de restaurer la version souhaitée de ce fichier.',
        'restore_version_error_only_one_version' => 'Il n\'existe qu\'une seule version de ce document.',
      
        'restoring' => 'Restauration en cours...',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Mettre ":document" à la corbeille?',
        'dialog_title_alt' => 'Mettre le document à la corbeille?',
        'dialog_title_count' => 'Mettre :count documents à la corbeille?',
        'dialog_text' => 'Vous allez mettre :document à la corbeille.',
        'dialog_text_count' => 'Vous allez mettre :document à la corbeille',
        'deleted_dialog_title' => ':document ont été mis à la corbeille',
        'deleted_dialog_title_alt' => 'Mis à la corbeille',
        'cannot_delete_dialog_title' => 'Impossible de mettre ":document" à la corbeille',
        'cannot_delete_dialog_title_alt' => 'Impossible de mettre à la corbeille',
        'cannot_delete_general_error' => 'Un problème est apparu en mettant ce document à la corbeille, veuillez contacter votre administrateur.',
    ],

    'permanent_delete' => [
        
        'dialog_title' => 'Effacer définitivement ":document"?',
        'dialog_title_alt' => 'Effacer définitivement ce document?',
        'dialog_title_count' => 'Effacer :count documents?',
        'dialog_text' => 'Vous allez effacer définitivement :document. Cette opération ne peut pas être annulée.',
        'dialog_text_count' => 'Vous allez effacer définitivement :count documents. Cette opération ne peut pas être annulée.',
        'deleted_dialog_title' => ':document a été définitivement effacé',
        'deleted_dialog_title_alt' => 'Effacé définitivement',
        'cannot_delete_dialog_title' => 'Impossible d\'effacer définitivement ":document"!',
        'cannot_delete_dialog_title_alt' => 'Impossible d\'effacer définitivement!',
        'cannot_delete_general_error' => 'Un problème est apparu en effaçant de document, veuillez contacter votre administrateur.',
    ],

    'preview' => [
        'page_title' => 'Aperçu de :document',
        'error' => 'Désolé, mais nous sommes incapables de charger l\'aperçu de ":document".',
        'not_available' => 'L\'aperçu de ce document ne peut être affiché.',
        'google_file_disclaimer' => ':document est un document Google Drive, nous ne pouvons pas afficher son aperçu ici. Vous devez l\'ouvrir dans Google Drive.',
        'google_file_disclaimer_alt' => 'Ceci est un document Google Drive et l\'aperçu ne peut être affiché ici.',
        'open_in_google_drive_btn' => 'Ouvrir dans Google Drive',
        'video_not_ready' => 'La vidéo est en cours de traitement. Elle sera disponible dans quelques secondes.',
    ],

    'versions' => [

        'section_title' => 'Versions',

        'section_title_with_count' => '1 version|:number versions',

        'version_count_label' => ':number version|:number versions',

        'version_number' => 'version :number',

        'version_current' => 'actuel',

        'new_version_button' => 'Mettre en ligne une nouvelle version',
        
        'new_version_button_uploading' => 'Mise en ligne en cours...',

        'filealreadyexists' => 'La version du fichier que vous mettez en ligne existe déjà dans la K-Box',
    ],

    'messages' => [
        'updated' => 'Les détails du document ont changé. Pendant le traitement des changements, le document peut ne pas encore être disponible dans les résultats de recherche.',
        'processing' => 'Le document est en cours de traitement par la K-Box. Il peut ne pas encore être disponible dans les résultats de recherche.',
        'local_public_only' => 'Affichage uniquement des docments publics de l\'institution.',
        'forbidden' => 'Vous n\'avez pas les autorisations nécessaires pour modifier ce document.',
        'delete_forbidden' => 'Vous n\'avez pas les autorisations nécessaires pour effacer des documents, veuillez contacter votre administrateur.',
        'delete_public_forbidden' => 'Vous ne pouvez pas effacer un document public. Veuillez contacter votre administrateur.',
        'delete_force_forbidden' => 'Vous ne pouvez pas effacer définitivement un document. Veuillez contacter votre administrateur.',
        'drag_hint' => 'Faites glisser le fichier pour commencer la mise en ligne.',
        'recent_hint_dms_manager' => 'Vous voyez actuellement toutes les modifications de documents faites par chaque utilisateur de la K-Box.',
        'no_documents' => 'Aucun document.  Vous pouvez ajouter des documents en utilisant le bouton "Créer ou ajouter" ci-dessus ou en les faisant glisser ici.',
    ],
    
    
    'trash' => [
        
        'clean_title' => 'Vider la corbeille?',
        'yes_btn' => 'Oui, vider',
        'no_btn' => 'Non',

        'empty_trash' => 'La corbeille est vide',
        
        'empty_all_text' => 'Tous les documents dans la corbeille vont être effacés définitivement. Cette action ne peut pas être annulée.',
        'empty_selected_text' => 'Vous allez effacer définitivement les documents sélectionnés. Cette action ne peut pas être annulée.',
        
        'cleaned' => 'Corbeille vidée',
        'cannot_clean' => 'Impossible de vider la corbeille',
        'cannot_clean_general_error' => 'Il y a eu un problème en vidant la corbeille. Veuillez contacter votre administrateur si le problème se répète.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Votre navigateur ne supporte pas le glisser-déposer.',
        'error_dialog_title' => 'Erreur pendant la mise en ligne du fichier',
        
        'max_uploads_reached_title' => 'Désolé, vous devez patienter encore un peu',
        'max_uploads_reached_text' => 'Nous pouvons traiter seulement un nombre limité de fichiers en même temps. Veuillez patienter un peu avant d\'ajouter un autre fichier.',
        
        'all_uploaded' => 'Tous les fichiers ont été mis en ligne avec succès.',
        
        'upload_dialog_title' => 'Mise en ligne',
        'page_title' => 'Mise en ligne',
        'dragdrop_not_supported' => 'Votre navigateur ne supporte pas la mise en ligne par glisser-déposer..',
        'dragdrop_not_supported_text' => 'Veuillez mettre vos fichiers en ligne en utilisant le sélecteur de fichiers dans "Créer ou ajouter".',
        'remove_btn' => "Supprimer le fichier", //this is the little link that is showed after the file upload has been processed
        'cancel_btn' => 'Annuler la mise en ligne', //for future use
        'cancel_question' => 'Etes-vous sûr de vouloir annuler cette mise en ligne?',  //for future use
        'outside_project_target_area' => 'Veuillez faire glisser votre fichier vers un projet pour le mettre en ligne.',
        'empty_file_error' => 'Fichier vide, veuillez mettre en ligne un fichier, qui contient au moins un mot.',
    ],
];
