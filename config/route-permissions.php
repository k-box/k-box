<?php

/*
|--------------------------------------------------------------------------
| Configuration that store what permission is required to access a route
|
| key   = the name of the route
| value = the permission required or the array of permissions
|         (permission are considered with logical OR)
|--------------------------------------------------------------------------
 */

return [

    /**
     * Search routes
     */
    
    'search-autocomplete' => KBox\Capability::MAKE_SEARCH,

    /**
     * Document routes
     */

    'documents' => [

        'index' => KBox\Capability::MAKE_SEARCH,
        'recent' => KBox\Capability::MAKE_SEARCH,
        'trash' => KBox\Capability::$CONTENT_MANAGER,
        'notindexed' => KBox\Capability::$CONTENT_MANAGER,
        'sharedwithme' => [KBox\Capability::RECEIVE_AND_SEE_SHARE, KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'show' => KBox\Capability::$CONTENT_MANAGER,
        'by-klink-id' => KBox\Capability::$CONTENT_MANAGER,
        'public_visibility' => KBox\Capability::MAKE_SEARCH,
        'visibility' => KBox\Capability::MAKE_SEARCH,
        'create' => KBox\Capability::UPLOAD_DOCUMENTS,
        'store' => KBox\Capability::UPLOAD_DOCUMENTS,
        'edit' => KBox\Capability::EDIT_DOCUMENT,
        'update' => KBox\Capability::EDIT_DOCUMENT,
        'destroy' => KBox\Capability::DELETE_DOCUMENT,
        
        'publish' => KBox\Capability::CHANGE_DOCUMENT_VISIBILITY,
        'unpublish' => KBox\Capability::CHANGE_DOCUMENT_VISIBILITY,

        'version' => [

            'show' => KBox\Capability::EDIT_DOCUMENT,
            'delete' => KBox\Capability::EDIT_DOCUMENT,
            'restore' => KBox\Capability::EDIT_DOCUMENT,
        ],

        'bulk' => [
            
            'restore' => KBox\Capability::DELETE_DOCUMENT,
            'remove' => KBox\Capability::DELETE_DOCUMENT,
            'emptytrash' => KBox\Capability::CLEAN_TRASH,
            'copyto' => KBox\Capability::$CONTENT_MANAGER,
            'makepublic' => KBox\Capability::CHANGE_DOCUMENT_VISIBILITY,
            'makeprivate' => KBox\Capability::CHANGE_DOCUMENT_VISIBILITY,

        ],

        'starred' => [

            'index' => KBox\Capability::MAKE_SEARCH,
            'store' => KBox\Capability::MAKE_SEARCH,
            'show' => KBox\Capability::MAKE_SEARCH,
            'destroy' => KBox\Capability::MAKE_SEARCH,

        ],

        'groups' => [

            'index' => KBox\Capability::$CONTENT_MANAGER,
            'create' => KBox\Capability::$CONTENT_MANAGER,
            'edit' => KBox\Capability::$CONTENT_MANAGER,
            'update' => KBox\Capability::$CONTENT_MANAGER,
            'store' => KBox\Capability::$CONTENT_MANAGER,
            'show' => KBox\Capability::$CONTENT_MANAGER,
            'destroy' => KBox\Capability::$CONTENT_MANAGER,

        ],

        'import' => [

            'index' => KBox\Capability::IMPORT_DOCUMENTS,
            'store' => KBox\Capability::IMPORT_DOCUMENTS,
            'clearcompleted' => KBox\Capability::IMPORT_DOCUMENTS,
            'destroy' => KBox\Capability::IMPORT_DOCUMENTS,
            'update' => KBox\Capability::IMPORT_DOCUMENTS,
            // 'status' => KBox\Capability::IMPORT_DOCUMENTS,

        ],

        'projects' => [

            'index' => KBox\Capability::MANAGE_PROJECT_COLLECTIONS,
            'show' => KBox\Capability::MANAGE_PROJECT_COLLECTIONS,

        ],
    
    ],

    'duplicates' => [
        'destroy' => KBox\Capability::EDIT_DOCUMENT,
    ],

    'shares' => [
        'index' => KBox\Capability::RECEIVE_AND_SEE_SHARE,
        'create' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'store' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'show' => KBox\Capability::RECEIVE_AND_SEE_SHARE,
        'group' => KBox\Capability::RECEIVE_AND_SEE_SHARE,
        'edit' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'update' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'destroy' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'deletemultiple' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
    ],
    
    'links' => [
        'store' => KBox\Capability::SHARE_WITH_PERSONAL,
        'show' => KBox\Capability::RECEIVE_AND_SEE_SHARE,
        'update' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
        'destroy' => [KBox\Capability::SHARE_WITH_PERSONAL, KBox\Capability::SHARE_WITH_PRIVATE],
    ],
    
    'people' => [
        'index' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'create' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'store' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'show' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'edit' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'update' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'destroy' => [KBox\Capability::MANAGE_PEOPLE_GROUPS, KBox\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
    ],
    
    'projects' => [
        'index' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'create' => [KBox\Capability::CREATE_PROJECTS],
        'store' => [KBox\Capability::CREATE_PROJECTS],
        'show' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'edit' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'update' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'destroy' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],

        'avatar' => [
            'index' => KBox\Capability::RECEIVE_AND_SEE_SHARE,
            'store' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
            'destroy' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        ]
    ],
    
    'microsites' => [
        'index' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'create' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'store' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'show' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'edit' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'update' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
        'destroy' => ['all' => KBox\Capability::$PROJECT_MANAGER_LIMITED],
    ],

    'import' => KBox\Capability::IMPORT_DOCUMENTS,
    'import-refresh' => KBox\Capability::IMPORT_DOCUMENTS,

    /**
     * Administration routes
     */

    'administration' => [

        'index' => KBox\Capability::MANAGE_DMS,

        'storage' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'reindexall' => KBox\Capability::MANAGE_DMS,
            'reindexstatus' => KBox\Capability::MANAGE_DMS,
            'naming' => KBox\Capability::MANAGE_DMS,
            'files' => KBox\Capability::MANAGE_DMS,
        ],

        'network' => [
            'index' => KBox\Capability::MANAGE_DMS,
        ],

        'maintenance' => [
            'index' => KBox\Capability::MANAGE_DMS,
        ],

        'mail' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'store' => KBox\Capability::MANAGE_DMS,
            'test' => KBox\Capability::MANAGE_DMS,
        ],
        
        'identity' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'store' => KBox\Capability::MANAGE_DMS,
        ],

        'users' => [
            'index' => KBox\Capability::MANAGE_USERS,
            'create' => KBox\Capability::MANAGE_USERS,
            'store' => KBox\Capability::MANAGE_USERS,
            'show' => KBox\Capability::MANAGE_USERS,
            'edit' => KBox\Capability::MANAGE_USERS,
            'update' => KBox\Capability::MANAGE_USERS,
            'destroy' => KBox\Capability::MANAGE_USERS,
        ],
        
        'messages' => [
            'index' => KBox\Capability::MANAGE_USERS,
            'create' => KBox\Capability::MANAGE_USERS,
            'store' => KBox\Capability::MANAGE_USERS,
            'show' => KBox\Capability::MANAGE_USERS,
            'edit' => KBox\Capability::MANAGE_USERS,
            'update' => KBox\Capability::MANAGE_USERS,
            'destroy' => KBox\Capability::MANAGE_USERS,
        ],
        
        'institutions' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'show' => KBox\Capability::MANAGE_DMS,
        ],
        
        'settings' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'create' => KBox\Capability::MANAGE_DMS,
            'store' => KBox\Capability::MANAGE_DMS,
            'show' => KBox\Capability::MANAGE_DMS,
            'edit' => KBox\Capability::MANAGE_DMS,
            'update' => KBox\Capability::MANAGE_DMS,
            'destroy' => KBox\Capability::MANAGE_DMS,
        ],

        'licenses' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'default' => ['update' => KBox\Capability::MANAGE_DMS],
            'available' => ['update' => KBox\Capability::MANAGE_DMS],
        ],

        'plugins' => [
            'index' => KBox\Capability::MANAGE_DMS,
            'show' => KBox\Capability::MANAGE_DMS,
            'edit' => KBox\Capability::MANAGE_DMS,
            'update' => KBox\Capability::MANAGE_DMS,
            'destroy' => KBox\Capability::MANAGE_DMS,
        ],
    ],

    /**
     * GeoPlugin route permission
     */

    'plugins' => [
        'k-box-kbox-plugin-geo' => [
            'geodocuments' => KBox\Capability::MAKE_SEARCH,
        ],
    ],

 ];
