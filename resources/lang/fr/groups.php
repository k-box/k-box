<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collections Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'collections' => [
        'title'        => 'Collections',
        'personal_title' => 'Mes Collections',
        'private_title' => 'Projets',
        'description'   => 'Une collection vous aide à organiser vos documents.',
        
        'empty_private_msg' => 'Aucun projet pour le moment.',

    ],

    'create_btn' => 'Créer',
    'save_btn' => 'Enregistrer',
    'loading' => 'Enregistrement de la collection en cours...',

    'panel_create_title' => 'Créer une nouvelle collection',

    'panel_edit_title' => 'Modifier la collection <strong>:name</strong>',

    'created_on' => 'créé le',
    'created_by' => 'créé par',

    'private_badge_label' => 'Collection de documents personnels',

    'group_icon_label' => 'Collection',

    'empty_msg' => 'Aucune collection. Créer une collection.',

    'form' => [
        'collection_name_placeholder' => 'Nom de la collection',
        'collection_name_label' => 'Nom de la collection',

        'parent_label' => 'Collection parente: <strong>:parent</strong>',
        'parent_project_label' => 'Dans la collection de projet: <strong>:parent</strong>',

        'make_public' => 'Rendre cette collection visible pour tous les utilisateurs du projet.',
        'make_private' => 'Rendre cette collection personnelle',
    ],
    
    
    
    'people' => [
        
        'page_title' => 'Groupes',
            
        'no_users' => 'Aucun utilisateur ne peut être ajouté à un groupe. Veuillez contacter votre administrateur ou vérifier que les utilisateurs peuvent recevoir et voir les partages.',
        
        'available_users' => 'Utilisateurs disponibles',
        'available_users_hint' => 'Glissez un utilisateur d\'ici vers un groupe pour l\'ajouter à ce groupe.',
       
        'remove_user' => 'Supprimer du groupe',
        
        'saving' => 'Enregistrement en cours...',
        
        'invalidargumentexception' => 'Désolé, cette opération ne peut être effectuée. :exception',
        
        'group_name_already_exists' => 'Un groupe avec le même nom existe déjà',
        'create_group_dialog_title' => 'Créer un groupe',
        'create_group_dialog_text' => 'le nom du groupe:',
        'create_group_dialog_placeholder' => 'Super groupe',
        'create_group_error_title' => 'La création du groupe a échoué',
        'create_group_generic_error_text' => 'Le groupe ne peut pas être créé et c\'est tout ce que nous savons.',
        
        'cannot_add_user_dialog_title' => 'Impossible de créer l\'utilisateur',
        'cannot_add_user_dialog_text' => 'L\'utilisateur ne peut pas être ajouté au groupe. Une erreur inattendue s\'est produite.',
        
        'user_already_exists' => 'L\'utilisateur ":name" existe déjà dans ce groupe',
        
        'delete_dialog_title' => 'Effacer ":name"?',
        'delete_dialog_text' => 'Supprimer le groupe ":name" de façon permanente? (cette opération ne peut pas être annulée)',
        'delete_error_title' => 'Impossible d\'effacer le groupe',
        'delete_generic_error_text' => 'Le groupe ne peut pas être effacé et c\'est tout ce que nous savons.',
        
        'remove_user_dialog_title' => 'Supprimer ":name"?',
        'remove_user_dialog_text' => 'Supprimer ":name" de ":group"?',
        'remove_user_error_title' => 'Impossible de supprimer l\'utilisateur de ce groupe',
        'remove_user_generic_error_text' => 'L\'utilisateur ne peut pas être supprimé et c\'est tout ce que nous savons.',
        
        'rename_dialog_title' => 'Renommer ":name"?',
        'rename_dialog_text' => 'le nom du groupe:',
        'rename_error_title' => 'Le renommage du groupe a échoué',
        'rename_generic_error_text' => 'Le groupe ne peut pas être renommé et c\'est tout ce que nous savons.',
    ],
    
    
    'delete' => [
        
        'dialog_title' => 'Effacer :collection?',
        'dialog_title_alt' => 'Effacer la collection?',
        'dialog_text' => 'Vous allez effacer :collection. Ceci va uniquement effacer la collection. Les documents de cette collection ne seront pas effacés.',
        'dialog_text_alt' => 'Vous allez effacer la collection sélectionnée. Ceci va uniquement effacer la collection. Les documents de cette collection ne seront pas effacés.',
        
        'deleted_dialog_title' => ':collection a été effacé',
        'deleted_dialog_title_alt' => 'Effacé',
        
        'cannot_delete_dialog_title' => 'Impossible d\'effacer ":collection"!',
        'cannot_delete_dialog_title_alt' => 'Impossible d\'effacer!',
        
        'cannot_delete_general_error' => 'Impossible d\'effacer les éléments spécifiés. Rien n\'a été effacé.',
        
        'forbidden_delete_collection' => 'La collection :collection ne peut pas être effacée. Vous n\'avez pas l\'autorisation d\'effectuer des opérations sur les collections.',
        'forbidden_delete_project_collection' => 'La collection :collection ne peut pas être effacée car elle se trouve dans un projet pour lequel vous n\'avez pas l\'autorisation de modifier les collections.',
    ],
    
    'move' => [
        'moved' => '":collection" déplacé',
        'moved_alt' => 'Déplacé',
        'moved_text' => 'La collection a été déplacée. Nous rafraîchissons votre visualisation...',
        'error_title' => 'Impossible de déplacer :collection',
        'error_title_alt' => 'Impossible de déplacer la collection',
        'error_text_generic' => 'L\'opération de déplacement n\'a pas pu être effectuée à cause d\'une erreur. Veuillez contacter votre administrateur.',
        'error_not_collection' => 'L\'opération de déplacement fonctionne uniquement pour les collections.',
        'error_same_collection' => 'Vous ne pouvez pas déplacer une collection sous elle-même.',
        'move_to_title' => 'Déplacer vers ":collection"?',
        'move_to_project_title' => 'Déplacer vers ":collection"?',
        'move_to_project_title_alt' => 'Déplacer vers le projet?',
        'move_to_project_text' => 'Vous allez déplacer une collection personnelle vers un projet. Cela va rendre ":collection" et ses sous-collections visibles pour tous les utilisateurs du projet.',
        'move_to_personal_title' => 'Rendre cette collection personnelle?',
        'move_to_personal_text' => 'Vous allez déplacer la collection d\'un projet vers vos collections personnelles. La collection ":collection" ne sera plus visible par les utilisateurs du projet.',
    ],
 
    'access' => [
        'forbidden' => 'Vous n\'avez pas l\'autorisation d\'accéder à ":name".',
        'forbidden_alt' => 'Vous ne pouvez pas accéder à la collection à cause de votre niveau d\'autorisation',
    ],
    
    'add_documents' => [
        'forbidden' => 'Vous ne pouvez pas ajouter des documents à ":name" car il vous manque les autorisations nécessaires.',
        'forbidden_alt' => 'Vous ne pouvez pas ajouter de documents à cette collection car il vous manque les autorisations nécessaires.',
    ],
    
    'remove_documents' => [
        'forbidden' => 'Vous ne pouvez pas supprimer des documents de ":name" car il vous manque les autorisations nécessaires.',
        'forbidden_alt' => 'Vous ne pouvez pas supprimer des documents de cette collection car il vous manque les autorisations nécessaires.',
    ],

];
