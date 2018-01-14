<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Projects related Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'page_title' => 'Projets',
    'page_title_with_name' => 'Projet :name',

    'all_projects' => 'Tous les projets',

    'new_button' => 'Nouveau projet',
    
    'create_page_title' => 'Créer un nouveau projet',
    'edit_page_title' => 'Modifier le projet :name',
    
    'edit_button' => 'Modifier',
    'delete_button' => 'Effacer',
    'close_edit_button' => 'Sortir du mode de modification',

    'labels' => [
        'name' => 'Nom du projet',
        'description' => 'Description du projet',
        'project_details' => 'Details du projet',
        
        'users' => 'Utilisateurs',
        'search_member_placeholder' => 'Rechercher un membre du projet...',
        'search_member_not_found' => 'Aucun membre avec ce nom ou cette institution.',
        'add_users' => 'Ajouter des utilisateurs au projet',
        'add_users_button' => 'Ajouter un utilisateur',
        'users_placeholder' => 'Sélectionner un / des utilisateur(s)',
        'users_hint' => 'Commencer à écrire ou sélectionner dans la liste déroulante les utilisateurs que vous souhaitez ajouter',
        
        
        'create_submit' => 'Créer un projet',
        'edit_submit' => 'Enregistrer le projet',
        'cancel' => 'Annuler',

        'users_in_project' => 'Membres du projet (:count)',

        'managed_by' => 'Géré par',
        'created_on' => 'Créé le',
        'user_count_label' => 'Membres',
        'user_count' => ':count membre|:count membres',
        'documents_count_label' => 'Documents',
        'documents_count' => ':count document|:count documents',

        'avatar' => 'Avatar du projet',
        'avatar_description' => 'La taille maximum du fichier est de 200KB. La résolution optimale est de 300 x 160 pixels.',
        'avatar_remove_btn' => 'Supprimer l\'avatar',
        'avatar_remove_confirmation' => 'L\'avatar du projet va être supprimé. Etes-vous sûr?',
        'avatar_remove_error_generic' => 'L\'avatar ne peut pas être supprimé.',
    ],

    'remove_user_hint' => 'Supprimer l\'utilisateur du projet',

    'removing_wait_title' => 'Suppression de l\'utilisateur en cours...',
    'removing_wait_text' => 'Suppression de l\'utilisateur du projet...',

    'no_user_available' => 'Aucun utilisateur enregistré ne peut être ajouté au projet. Il est possible que vous ayiez déjà ajouté tous les utilisateurs.',
    
    'no_members' => 'Il n\'y a pas encore d\'utilisateurs dans ce projet. Commencez pas ajouter quelqu\'un.',

    'empty_selection' => 'Sélectionnez un projet pour voir les détails',
    'empty_projects' => 'Aucun projet. <a href=":url">Créez</a> un nouveau projet',
    
    'errors' => [

        'exception' => 'Le projet ne peut pas être créé. (:exception)',
        
        'prevent_edit_description' => 'La collection de projet ne peut pas être modifiée depuis ici. Veuillez vous rendre dans <a href=":link">Projects > Modifier :name</a> pour faire les changements.',
        
        'prevent_delete_description' => 'La collection de projet ne peut pas être effacée.'
    ],
    
    'project_created' => 'Le projet :name a été créé',
    
    'project_updated' => 'Le projet :name a été mis à jour',
    
    'no_projects' => 'Il n\'y a actuellement aucun projet à lister ici.',

    'show_documents' => 'Montrer les documents',
];
