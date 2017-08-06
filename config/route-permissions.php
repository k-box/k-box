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
     * User's profile page
     */
//
//    'profile' => array(
//		'index' => KlinkDMS\Capability::MAKE_SEARCH,
//		'store' => KlinkDMS\Capability::MAKE_SEARCH,
//		'update' => KlinkDMS\Capability::MAKE_SEARCH,
//		'show' => KlinkDMS\Capability::MAKE_SEARCH,
//	),

    /**
     * Search routes
     */
    
    'search-autocomplete' => KlinkDMS\Capability::MAKE_SEARCH,

    /**
     * Document routes
     */

    'documents' => [

        'index' => KlinkDMS\Capability::MAKE_SEARCH,
        'recent' => KlinkDMS\Capability::MAKE_SEARCH,
        'trash' => KlinkDMS\Capability::$CONTENT_MANAGER,
        'notindexed' => KlinkDMS\Capability::$CONTENT_MANAGER,
        'sharedwithme' => [KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE, KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'show' => KlinkDMS\Capability::$CONTENT_MANAGER,
        'by-klink-id' => KlinkDMS\Capability::$CONTENT_MANAGER,
        'visibility' => KlinkDMS\Capability::MAKE_SEARCH,
        'create' => KlinkDMS\Capability::UPLOAD_DOCUMENTS,
        'store' => KlinkDMS\Capability::UPLOAD_DOCUMENTS,
        'edit' => KlinkDMS\Capability::EDIT_DOCUMENT,
        'update' => KlinkDMS\Capability::EDIT_DOCUMENT,
        'destroy' => KlinkDMS\Capability::DELETE_DOCUMENT,
        

        'bulk' => [
            
            'restore' => KlinkDMS\Capability::DELETE_DOCUMENT,
            'remove' => KlinkDMS\Capability::DELETE_DOCUMENT,
            'emptytrash' => KlinkDMS\Capability::CLEAN_TRASH,
            'copyto' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'makepublic' => KlinkDMS\Capability::CHANGE_DOCUMENT_VISIBILITY,
            'makeprivate' => KlinkDMS\Capability::CHANGE_DOCUMENT_VISIBILITY,

        ],

        'starred' => [

            'index' => KlinkDMS\Capability::MAKE_SEARCH,
            'store' => KlinkDMS\Capability::MAKE_SEARCH,
            'show' => KlinkDMS\Capability::MAKE_SEARCH,
            'destroy' => KlinkDMS\Capability::MAKE_SEARCH,

        ],

        'groups' => [

            'index' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'create' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'edit' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'update' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'store' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'show' => KlinkDMS\Capability::$CONTENT_MANAGER,
            'destroy' => KlinkDMS\Capability::$CONTENT_MANAGER,

        ],

        'import' => [

            'index' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
            'store' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
            'clearcompleted' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
            'destroy' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
            'update' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
            // 'status' => KlinkDMS\Capability::IMPORT_DOCUMENTS,

        ],

        'projects' => [

            'index' => KlinkDMS\Capability::MANAGE_PROJECT_COLLECTIONS,
            'show' => KlinkDMS\Capability::MANAGE_PROJECT_COLLECTIONS,

        ],
    
    ],

    'shares' => [
        'index' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
        'create' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'store' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'show' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
        'group' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
        'edit' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'update' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'destroy' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'deletemultiple' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
    ],
    
    'links' => [
        'store' => KlinkDMS\Capability::SHARE_WITH_PERSONAL,
        'show' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
        'update' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
        'destroy' => [KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE],
    ],
    
    
    'people' => [
        'index' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'create' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'store' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'show' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'edit' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'update' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
        'destroy' => [KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS],
    ],
    
    'projects' => [
        'index' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'create' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'store' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'show' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'edit' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'update' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'destroy' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],

        'avatar' => [
            'index' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
            'store' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
            'destroy' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        ]
    ],
    
    'microsites' => [
        'index' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'create' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'store' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'show' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'edit' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'update' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        'destroy' => ['all' => KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
    ],

    'import' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
    'import-refresh' => KlinkDMS\Capability::IMPORT_DOCUMENTS,

    /**
     * Administration routes
     */

    'administration' => [

        'index' => KlinkDMS\Capability::MANAGE_DMS,

        'storage' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
            'reindexall' => KlinkDMS\Capability::MANAGE_DMS,
            'reindexstatus' => KlinkDMS\Capability::MANAGE_DMS,
            'naming' => KlinkDMS\Capability::MANAGE_DMS,
        ],

        'network' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
        ],

        'maintenance' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
        ],

        'mail' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
            'store' => KlinkDMS\Capability::MANAGE_DMS,
            'test' => KlinkDMS\Capability::MANAGE_DMS,
        ],
        
        'identity' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
            'store' => KlinkDMS\Capability::MANAGE_DMS,
        ],

        'users' => [
            'index' => KlinkDMS\Capability::MANAGE_USERS,
            'create' => KlinkDMS\Capability::MANAGE_USERS,
            'store' => KlinkDMS\Capability::MANAGE_USERS,
            'show' => KlinkDMS\Capability::MANAGE_USERS,
            'edit' => KlinkDMS\Capability::MANAGE_USERS,
            'update' => KlinkDMS\Capability::MANAGE_USERS,
            'destroy' => KlinkDMS\Capability::MANAGE_USERS,
        ],
        
        'messages' => [
            'index' => KlinkDMS\Capability::MANAGE_USERS,
            'create' => KlinkDMS\Capability::MANAGE_USERS,
            'store' => KlinkDMS\Capability::MANAGE_USERS,
            'show' => KlinkDMS\Capability::MANAGE_USERS,
            'edit' => KlinkDMS\Capability::MANAGE_USERS,
            'update' => KlinkDMS\Capability::MANAGE_USERS,
            'destroy' => KlinkDMS\Capability::MANAGE_USERS,
        ],
        
        'institutions' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
            'create' => KlinkDMS\Capability::MANAGE_DMS,
            'store' => KlinkDMS\Capability::MANAGE_DMS,
            'show' => KlinkDMS\Capability::MANAGE_DMS,
            'edit' => KlinkDMS\Capability::MANAGE_DMS,
            'update' => KlinkDMS\Capability::MANAGE_DMS,
            'destroy' => KlinkDMS\Capability::MANAGE_DMS,
        ],
        
        'settings' => [
            'index' => KlinkDMS\Capability::MANAGE_DMS,
            'create' => KlinkDMS\Capability::MANAGE_DMS,
            'store' => KlinkDMS\Capability::MANAGE_DMS,
            'show' => KlinkDMS\Capability::MANAGE_DMS,
            'edit' => KlinkDMS\Capability::MANAGE_DMS,
            'update' => KlinkDMS\Capability::MANAGE_DMS,
            'destroy' => KlinkDMS\Capability::MANAGE_DMS,
        ],
    ],

 ];
