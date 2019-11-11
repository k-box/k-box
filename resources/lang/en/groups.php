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
        'shared_title' => 'Collections shared with me',
        'private_title' => 'Projects',
        'description'   => 'Collections help to organize your documents.',
        
        'empty_private_msg' => 'No Projects at the moment.',

    ],

    'create_btn' => 'Create',
    'save_btn' => 'Save',
    'loading' => 'Saving collection...',

    'panel_create_title' => 'Create new Collection',

    'panel_edit_title' => 'Edit Collection <strong>:name</strong>',

    'created_on' => 'created on',
    'created_by' => 'created by',

    'private_badge_label' => 'Personal Document Collection',

    'group_icon_label' => 'Collection',
    'group_icon_label_personal' => 'Personal collection',
    'group_icon_label_project' => 'Project collection',

    'empty_msg' => 'No collections - create one.',

    'form' => [
        'collection_name_placeholder' => 'Title of the collection',
        'collection_name_label' => 'Collection name',

        'parent_label' => 'In the Private Collection: <strong>:parent</strong>',
        'parent_project_label' => 'In the Project Collection: <strong>:parent</strong>',

        'make_public' => 'Make this collection visible to users of the Project.',
        'make_private' => 'Make this Collection personal',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Delete :collection?',
        'dialog_title_alt' => 'Delete collection?',
        'dialog_text' => 'You are about to delete :collection. This will delete the collection and will remove it from the documents. The documents will not be deleted.',
        'dialog_text_alt' => 'You are about to delete the selected Collection. This will delete the collection and will remove it from the documents. The documents will not be deleted.',
        
        'deleted_dialog_title' => ':collection deleted',
        'deleted_dialog_title_alt' => 'Deleted',
        
        'cannot_delete_dialog_title' => 'Cannot delete ":collection"',
        'cannot_delete_dialog_title_alt' => 'Cannot delete',
        
        'cannot_delete_general_error' => 'Cannot delete the specified elements. Nothing has been deleted.',
        
        'forbidden_trash_personal_collection' => 'You did not create :collection, therefore you cannot trash it.',
        'forbidden_delete_shared_collection' => '":collection" has been shared with you, therefore you cannot trash it.',
        'forbidden_delete_personal_collection' => 'You did not create :collection, therefore you cannot delete it.',
        'forbidden_delete_collection' => 'The collection :collection cannot be deleted. You are not allowed to operate on Collections.',
        'forbidden_delete_project_collection' => 'The collection :collection cannot be deleted as it is in a project where you do not have the edit permissions.',
        'forbidden_delete_project_collection_not_creator' => 'You are not the creator of the collection :collection, therefore you cannot delete it.',
        'forbidden_delete_project_collection_not_manager' => 'You are not the manager of the project that contained :collection, therefore you cannot delete it.',
    ],
    
    'move' => [
        'moved' => '":collection" moved',
        'moved_alt' => 'Moved',
        'moved_text' => 'The collection has been moved, we are refreshing your visualization...',
        'error_title' => 'Cannot move :collection',
        'error_title_alt' => 'Cannot move collection',
        'error_text_generic' => 'The move operation cannot be performed due to an error. Please contact your K-Box Administrator.',
        'error_not_collection' => 'The move action applies only to collections.',
        'error_same_collection' => 'You cannot move a collection under itself.',
        'move_to_title' => 'Move to ":collection"?',
        'move_to_project_title' => 'Move to ":collection"?',
        'move_to_project_title_alt' => 'Move to Project?',
        'move_to_project_text' => 'You are about to move a personal collection under a Project. This will make ":collection" and its sub-collections visible to all users of the Project.',
        'move_to_personal_title' => 'Make Collection personal?',
        'move_to_personal_text' => 'You are about to move out a Project collection to your personal collections. The collection ":collection" will not be seen anymore by the other users of the project.',

        'errors' => [
            'personal_not_all_same_user' => 'Cannot move ":collection" to your personal. You are not the creator of :collection_cause',
            'personal_not_all_same_user_empty_cause' => 'Cannot move ":collection" to your personal as you are not the creator of it',
            'no_project_collection_permission' => 'You do not have the necessary permission to move a project collection',
            'no_access_to_collection' => 'You do not have access to the collection',
            'has_shares_to_non_members' => 'Some users of the collection are not project members. You cannot move it until they are included into the project. Add new members to the project before moving the collection.',
        ],

    ],
    
    'access' => [
        'forbidden' => 'You do not have the rights to access ":name".',
        'forbidden_alt' => 'You cannot access the collection due to lack of permission',
    ],
    
    'add_documents' => [
        'forbidden' => 'You cannot add documents to ":name" because you lack the required permissions.',
        'forbidden_alt' => 'You cannot add documents to the collection due to lack of permissions',
    ],
    
    'remove_documents' => [
        'forbidden' => 'You cannot remove documents from ":name" because you lack the required permissions.',
        'forbidden_alt' => 'You cannot remove documents from the collection due to lack of permissions',
    ],

];
