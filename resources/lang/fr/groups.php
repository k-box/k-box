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
        'personal_title' => 'My Collections',
        'private_title' => 'Projects',
        'description'   => 'Collection helps organize your documents.',
        
        'empty_private_msg' => 'No Projects at the moment.',

    ],

    'create_btn' => 'Create',
    'save_btn' => 'Save',
    'loading' => 'Saving collection...',

    'panel_create_title' => 'Create a new Collection',

    'panel_edit_title' => 'Edit Collection <strong>:name</strong>',

    'created_on' => 'created on',
    'created_by' => 'created by',

    'private_badge_label' => 'Personal Document Collection',

    'group_icon_label' => 'Collection',

    'empty_msg' => 'No collections. Create a collection.',

    'form' => [
        'collection_name_placeholder' => 'Name of the collection',
        'collection_name_label' => 'Collection name',

        'parent_label' => 'Parent Collection',
        'parent_project_label' => 'In the Project Collection',

        'make_public' => 'Make this collection visible to users of the Project.',
        'make_private' => 'Make this Collection personal',
    ],
    
    
    
    'people' => [
        
        'page_title' => 'Groups',
            
        'no_users' => 'No User can be added to a group, please contact your administrator or verify that the users can receive and see shares.',
        
        'available_users' => 'Available Users',
        'available_users_hint' => 'Drag a user from here to a group to add that user to the group.',
        
        'remove_user' => 'Remove from group',
        
        'saving' => 'Saving...',
        
        'invalidargumentexception' => 'Sorry, The operation cannot be performed. :exception',
        
        'group_name_already_exists' => 'A group with the same name already exists',
        'create_group_dialog_title' => 'Create Group',
        'create_group_dialog_text' => 'the name of the group:',
        'create_group_dialog_placeholder' => 'Awesome group',
        'create_group_error_title' => 'Group creation failed',
        'create_group_generic_error_text' => 'The group cannot be created and is all we know.',
        
        'cannot_add_user_dialog_title' => 'Cannot add user',
        'cannot_add_user_dialog_text' => 'The user cannot be added to the group. An unexpected error occurred.',
        
        'user_already_exists' => 'User ":name" already exists in the group',
        
        'delete_dialog_title' => 'Delete ":name"?',
        'delete_dialog_text' => 'Remove the group ":name" permanently? (the operation cannot be undone)',
        'delete_error_title' => 'Cannot delete group',
        'delete_generic_error_text' => 'The group cannot be deleted and is all we know.',
        
        'remove_user_dialog_title' => 'Remove ":name"?',
        'remove_user_dialog_text' => 'Remove ":name" from ":group"?',
        'remove_user_error_title' => 'Cannot remove user from group',
        'remove_user_generic_error_text' => 'The user cannot be removed and is all we know.',
        
        'rename_dialog_title' => 'Rename ":name" to?',
        'rename_dialog_text' => 'the name of the group:',
        'rename_error_title' => 'Group rename failed',
        'rename_generic_error_text' => 'The group cannot be renamed and is all we know.',
    ],
    
    
    'delete' => [
        
        'dialog_title' => 'Delete :collection?',
        'dialog_title_alt' => 'Delete collection?',
        'dialog_text' => 'You’re about to delete :collection. This will delete only the collection and will remove it from the documents. The documents will not be deleted.',
        'dialog_text_alt' => 'You’re about to delete the selected Collection. This will delete only the collection and will remove it from the documents. The documents will not be deleted.',
        
        'deleted_dialog_title' => ':collection has been deleted',
        'deleted_dialog_title_alt' => 'Deleted',
        
        'cannot_delete_dialog_title' => 'Cannot delete ":collection"!',
        'cannot_delete_dialog_title_alt' => 'Cannot delete!',
        
        'cannot_delete_general_error' => 'Cannot delete the specified elements. Nothing has been deleted.',
        
        'forbidden_delete_collection' => 'The collection :collection cannot be deleted. You are not allowed to operate on Collections.',
        'forbidden_delete_project_collection' => 'The collection :collection cannot be deleted as it is in a project where you don\'t have the rights to edit the collections.',
    ],
    
    'move' => [
        'moved' => '":collection" Moved',
        'moved_alt' => 'Moved',
        'moved_text' => 'The collection has been moved, we are refreshing your visualization...',
        'error_title' => 'Cannot move :collection',
        'error_title_alt' => 'Cannot move collection',
        'error_text_generic' => 'The move operation cannot be performed due to an error, please contact your DMS Administrator.',
        'error_not_collection' => 'The move action applies only to collections.',
        'error_same_collection' => 'You cannot move a collection under itself.',
        'move_to_title' => 'Move to ":collection"?',
        'move_to_project_title' => 'Move to ":collection"?',
        'move_to_project_title_alt' => 'Move to Project?',
        'move_to_project_text' => 'You are about to move a personal collection under a Project. This will make ":collection", and its sub-collections, visible to all users of the Project.',
        'move_to_personal_title' => 'Make Collection personal?',
        'move_to_personal_text' => 'You are about to move out a Project collection to your personal collections. The collection ":collection" will not be seen anymore by the other users of the project.',
    ],
    
    'access' => [
        'forbidden' => 'You don’t have the rights to access ":name".',
        'forbidden_alt' => 'You cannot access the collection due to permission levels',
    ],
    
    'add_documents' => [
        'forbidden' => 'You cannot add documents to ":name" because you lack the required authorizations.',
        'forbidden_alt' => 'You cannot add documents to the collection due to lack of permission',
    ],
    
    'remove_documents' => [
        'forbidden' => 'You cannot remove documents from ":name" because you lack the required authorizations.',
        'forbidden_alt' => 'You cannot remove documents from the collection due to lack of permission',
    ],

];
