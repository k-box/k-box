<?php

return [

	/*
	|--------------------------------------------------------------------------
	| K-Link DMS Version (aka Application version)
	|--------------------------------------------------------------------------
	 */
	'version' => '0.6.0',
	
	/*
	|--------------------------------------------------------------------------
	| K-Link DMS Edition. Used when upgrading for a version to a new version
	|--------------------------------------------------------------------------
	 */
	'edition' => 'project',


	/*
	|--------------------------------------------------------------------------
	| DMS Identifier
	|--------------------------------------------------------------------------
	|
	| The unique identifier for the DMS instance
	|
	| @var string
	*/

	'identifier' => getenv('DMS_IDENTIFIER') ?: '4815162342',

	/*
	|--------------------------------------------------------------------------
	| Institution Identifier
	|--------------------------------------------------------------------------
	|
	| The institution identifier that is required for communicating with the 
	| K-Link Core
	|
	| @var string
	*/

	'institutionID' => getenv('DMS_INSTITUTION_IDENTIFIER') ?: 'DMS',

	/*
	|--------------------------------------------------------------------------
	| Guest public searches
	|--------------------------------------------------------------------------
	|
	| Tell if the DMS will allow guest user to perform public search over K-Link
	|
	| @var boolean
	*/
	'are_guest_public_search_enabled' => getenv('DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED') ?: true,


	'core' => array(

		/*
		|--------------------------------------------------------------------------
		| K-Link Core URL
		|--------------------------------------------------------------------------
		|
		| The url of the Private K-Link Core
		|
		| @var string
		*/

		'address' => getenv('DMS_CORE_ADDRESS') ?: null,

		/*
		|--------------------------------------------------------------------------
		| K-Link Core username
		|--------------------------------------------------------------------------
		|
		| The username for authenticating on the Private K-Link Core
		|
		| @var string
		*/

		'username' => getenv('DMS_CORE_USERNAME') ?: null,

		/*
		|--------------------------------------------------------------------------
		| K-Link Core password
		|--------------------------------------------------------------------------
		|
		| The password for authenticating on the Private K-Link Core
		|
		| @var string
		*/

		'password' => getenv('DMS_CORE_PASSWORD') ?: null,

	),

	/*
	|--------------------------------------------------------------------------
	| Default Document Visibility
	|--------------------------------------------------------------------------
	|
	| The document visibility (private or public) that will be used as default
	| for the newly added documents that needs to be indexed in the K-Link Core
	| 
	| default: private
	| @var string public or private
	|
	*/

	'default_document_visibility' => getenv('DMS_DEFAULT_DOCUMENT_VISIBILITY') ?: 'private',

	/*
	|--------------------------------------------------------------------------
	| Enable user activity tracking
	|--------------------------------------------------------------------------
	|
	| Enable or Disable the user activity tracking
	| K-Link Core
	|
	| default: true
	|
	| @var boolean
	*/

	'enable_activity_tracking' => !!getenv('DMS_ENABLE_ACTIVITY_TRACKING') ?: true,

	/*
	|--------------------------------------------------------------------------
	| Number of items to display per page
	|--------------------------------------------------------------------------
	|
	|
	| default: 12
	|
	| @var integer
	*/

	'items_per_page' => getenv('DMS_ITEMS_PER_PAGE') ?: 16,

	/*
	|--------------------------------------------------------------------------
	| Upload folder
	|--------------------------------------------------------------------------
	| 
	| Where the files will be uploaded
	|
	| default: /storage/uploads
	|
	| @var string
	*/

	'upload_folder' => getenv('DMS_UPLOAD_FOLDER') ?: storage_path('documents/'),

	/*
	|--------------------------------------------------------------------------
	| File Upload Maximum size (in KB)
	|--------------------------------------------------------------------------
	| 
	| The maximum size of the file allowed for upload in kilobytes
	|
	| default: 30000
	|
	| @var integer
	*/

	'max_upload_size' => getenv('DMS_MAX_UPLOAD_SIZE') ?: 202800,

	/*
	|--------------------------------------------------------------------------
	| Allowed File Upload types
	|--------------------------------------------------------------------------
	| 
	| A comma separated list of allowed file types to be uploaded
	|
	| default: docx,doc,xlsx,xls,pptx,ppt,pdf,txt,jpg,gif,png,odt,odp,ods
	|
	| @var string
	*/

	'allowed_file_types' => getenv('DMS_ALLOWED_FILE_TYPES') ?: 'docx,doc,xlsx,xls,pptx,ppt,pdf,txt,jpg,gif,png,odt,odp,ods,md,txt,rtf,kmz,kml,gdoc,gslides,gsheet',

	/*
	|--------------------------------------------------------------------------
	| Use HTTPS as default url schema
	|--------------------------------------------------------------------------
	| 
	| Use HTTPS for serving the pages
	|
	| default: true
	|
	| @var boolean
	*/

	'use_https' => getenv('DMS_USE_HTTPS') ?: true,
	
	/*
	|--------------------------------------------------------------------------
	| Feedback Widget Key
	|--------------------------------------------------------------------------
	| 
	| The UserVoice feedback key 
	|
	|
	| @var string
	*/
	
	'feedback_api_key' => getenv('FEEDBACK_API_KEY') ?: 'O2C19h6uGprEjDfhiFDQ',
	
	
	/*
	|--------------------------------------------------------------------------
	| Limit languages filters to
	|--------------------------------------------------------------------------
	| 
	| Use this option to limit the usable language filters
	|
	| default: false
	| acceptable value: array of comma separated language codes e.g. en,ru,de
	|
	| @var boolean|string
	*/
	
	'limit_languages_to' => getenv('DMS_LIMIT_LANGUAGES_TO') ?: false,
	
];
