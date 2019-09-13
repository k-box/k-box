<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Projects related Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'page_title' => 'Projects',
    'page_title_with_name' => 'Project :name',

    'all_projects' => 'All Projects',

    'new_button' => 'New Project',
    
    'create_page_title' => 'Create New Project',
    'edit_page_title' => 'Edit Project :name',
    
    'edit_button' => 'Edit',
    'delete_button' => 'Delete',
    'close_edit_button' => 'Exit edit mode',

    'labels' => [
        'name' => 'Project Name',
        'description' => 'Project Description',
        'project_details' => 'Project Details',
        
        'users' => 'Users',
        'search_member_placeholder' => 'Search for a project member...',
        'search_member_not_found' => 'No member with that name or institution.',
        'add_users' => 'Add users to the project',
        'add_users_button' => 'Add User',
        'users_placeholder' => 'Select user(s)',
        'users_hint' => 'Start typing or select the users you would like to add from the dropdown',
        
        
        'create_submit' => 'Create Project',
        'edit_submit' => 'Save Project',
        'cancel' => 'Cancel',

        'users_in_project' => 'Current project members (:count)',

        'managed_by' => 'Managed by',
        'created_on' => 'Created on',
        'user_count_label' => 'Members',
        'user_count' => ':count member|:count members',
        'documents_count_label' => 'Documents',
        'documents_count' => ':count document|:count documents',

        'avatar' => 'Project avatar',
        'avatar_description' => 'The maximum file size allowed is 200KB. Best viewed if 300 x 160 pixels.',
        'avatar_remove_btn' => 'Remove avatar',
        'avatar_remove_confirmation' => 'The project avatar will be removed. Are you sure?',
        'avatar_remove_error_generic' => 'The avatar cannot be removed.',
    ],

    'remove_user_hint' => 'Remove the user from the Project',

    'removing_wait_title' => 'Removing user...',
    'removing_wait_text' => 'Removing user from the project...',

    'no_user_available' => 'No registered user can be added to the project. It might be the case that you added all the users.',
    
    'no_members' => 'There are no users in the project yet. Start adding someone.',

    'empty_selection' => 'Select a project to see the details',
    'empty_projects' => 'No projects. <a href=":url">Create</a> a new project',
    
    'errors' => [

        'exception' => 'The project cannot be created. (:exception)',
        
        'already_existing' => 'A project with the same name already exists.',
        
        'prevent_edit_description' => 'The Project collection cannot be edited from here. Please goto <a href=":link">Projects > Edit :name</a> to make the edits.',
        
        'prevent_delete_description' => 'The Project collection cannot be deleted.'
    ],
    
    'project_created' => 'Project :name created',
    
    'project_updated' => 'Project :name updated',
    
    'no_projects' => 'Currently there are no projects to list here.',

    'show_documents' => 'Show documents',
];
