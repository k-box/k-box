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

return array( 

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

	'documents' => array(

		'index' => KlinkDMS\Capability::MAKE_SEARCH,
		'recent' => KlinkDMS\Capability::MAKE_SEARCH,
		'trash' => KlinkDMS\Capability::$CONTENT_MANAGER,
		'notindexed' => KlinkDMS\Capability::$CONTENT_MANAGER,
		'sharedwithme' => array(KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE, KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
		'show' => KlinkDMS\Capability::$CONTENT_MANAGER,
		'by-klink-id' => KlinkDMS\Capability::$CONTENT_MANAGER,
		'visibility' => KlinkDMS\Capability::MAKE_SEARCH,
		'create' => KlinkDMS\Capability::UPLOAD_DOCUMENTS,
		'store' => KlinkDMS\Capability::UPLOAD_DOCUMENTS,
		'edit' => KlinkDMS\Capability::EDIT_DOCUMENT,
		'update' => KlinkDMS\Capability::EDIT_DOCUMENT,
		'destroy' => KlinkDMS\Capability::DELETE_DOCUMENT,
		

		'bulk' => array(
			
			'restore' => KlinkDMS\Capability::DELETE_DOCUMENT,
			'remove' => KlinkDMS\Capability::DELETE_DOCUMENT,
			'copyto' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'makepublic' => KlinkDMS\Capability::CHANGE_DOCUMENT_VISIBILITY,

		),

		'starred' => array(

			'index' => KlinkDMS\Capability::MAKE_SEARCH,
			'store' => KlinkDMS\Capability::MAKE_SEARCH,
			'show' => KlinkDMS\Capability::MAKE_SEARCH,
			'destroy' => KlinkDMS\Capability::MAKE_SEARCH,

		),

		'groups' => array(

			'index' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'create' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'edit' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'update' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'store' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'show' => KlinkDMS\Capability::$CONTENT_MANAGER,
			'destroy' => KlinkDMS\Capability::$CONTENT_MANAGER,

		),

		'import' => array(

			'index' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
			'store' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
			'clearcompleted' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
			// 'destroy' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
			// 'status' => KlinkDMS\Capability::IMPORT_DOCUMENTS,

		),
	
	),

	'shares' => array(
		'index' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
		'create' => array(KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
		'store' => array(KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
		'show' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
		'group' => KlinkDMS\Capability::RECEIVE_AND_SEE_SHARE,
		'edit' => array(KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
		'update' => array(KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
		'destroy' => array(KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
		'deletemultiple' => array(KlinkDMS\Capability::SHARE_WITH_PERSONAL, KlinkDMS\Capability::SHARE_WITH_PRIVATE),
	),
	
	
	'people' => array(
		'index' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
		'create' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
		'store' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
		'show' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
		'edit' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
		'update' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
		'destroy' => array(KlinkDMS\Capability::MANAGE_PEOPLE_GROUPS, KlinkDMS\Capability::MANAGE_PERSONAL_PEOPLE_GROUPS),
	),
	
	'projects' => array(
		'index' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'create' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'store' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'show' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'edit' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'update' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'destroy' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
	),
    
    'microsites' => array(
		'index' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'create' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'store' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'show' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'edit' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'update' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
		'destroy' => array('all' => KlinkDMS\Capability::$PROJECT_MANAGER),
	),

	'import' => KlinkDMS\Capability::IMPORT_DOCUMENTS,
   	'import-refresh' => KlinkDMS\Capability::IMPORT_DOCUMENTS,

	/**
	 * Administration routes
	 */

	'administration' => array(

		'index' => KlinkDMS\Capability::MANAGE_DMS,

		'storage' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
			'reindexall' => KlinkDMS\Capability::MANAGE_DMS,
			'reindexstatus' => KlinkDMS\Capability::MANAGE_DMS,
			'naming' => KlinkDMS\Capability::MANAGE_DMS,
		),

		'languages' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
		),

		'network' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
		),

		'maintenance' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
		),

		'mail' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
			'store' => KlinkDMS\Capability::MANAGE_DMS,
			'test' => KlinkDMS\Capability::MANAGE_DMS,
		),

		'users' => array(
			'index' => KlinkDMS\Capability::MANAGE_USERS,
			'create' => KlinkDMS\Capability::MANAGE_USERS,
			'store' => KlinkDMS\Capability::MANAGE_USERS,
			'show' => KlinkDMS\Capability::MANAGE_USERS,
			'edit' => KlinkDMS\Capability::MANAGE_USERS,
			'update' => KlinkDMS\Capability::MANAGE_USERS,
			'destroy' => KlinkDMS\Capability::MANAGE_USERS,
		),
		
		'messages' => array(
			'index' => KlinkDMS\Capability::MANAGE_USERS,
			'create' => KlinkDMS\Capability::MANAGE_USERS,
			'store' => KlinkDMS\Capability::MANAGE_USERS,
			'show' => KlinkDMS\Capability::MANAGE_USERS,
			'edit' => KlinkDMS\Capability::MANAGE_USERS,
			'update' => KlinkDMS\Capability::MANAGE_USERS,
			'destroy' => KlinkDMS\Capability::MANAGE_USERS,
		),
		
		'institutions' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
			'create' => KlinkDMS\Capability::MANAGE_DMS,
			'store' => KlinkDMS\Capability::MANAGE_DMS,
			'show' => KlinkDMS\Capability::MANAGE_DMS,
			'edit' => KlinkDMS\Capability::MANAGE_DMS,
			'update' => KlinkDMS\Capability::MANAGE_DMS,
			'destroy' => KlinkDMS\Capability::MANAGE_DMS,
		),
		
		'settings' => array(
			'index' => KlinkDMS\Capability::MANAGE_DMS,
			'create' => KlinkDMS\Capability::MANAGE_DMS,
			'store' => KlinkDMS\Capability::MANAGE_DMS,
			'show' => KlinkDMS\Capability::MANAGE_DMS,
			'edit' => KlinkDMS\Capability::MANAGE_DMS,
			'update' => KlinkDMS\Capability::MANAGE_DMS,
			'destroy' => KlinkDMS\Capability::MANAGE_DMS,
		),
	),

 );