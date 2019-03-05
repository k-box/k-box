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
