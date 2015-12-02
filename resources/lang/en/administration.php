<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Administration Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used inside the DMS Administration area
	|
	*/

	'page_title' => 'Administration',

	'menu' => array(

		'accounts'=>'Accounts',
		'language'=>'Language',
		'storage'=>'Storage',
		'network'=>'Network',
		'mail'=>'Mail',
		'update'=>'Update and recovery',
		'maintenance'=>'Maintenance and Events',
		'settings'=>'Settings',

	),

	'accounts' => array(

		'disable_confirm' => 'Do you really want to disable :name',

		'create_user_btn' => 'Create User',

		'table' => array(

			'name_column' => 'name',
			'email_column' => 'email',

		),
		
		'edit_account_title' => 'Edit :name',

		'labels' => array(

			'email' => 'Mail',
			'username' => 'Username',
			'perms' => 'Permissions',

			'cancel' => 'Cancel',

			'create' => 'Create',
			'update' => 'Update',

		),

		'capabilities' => array(

			'manage_dms' => 'The user can access to the DMS administration panel',
			'manage_dms_users' => 'The user can manage DMS users',
			'manage_dms_log' => 'The user can see the DMS logs',
			'manage_dms_backup' => 'The user can perform DMS backups and restore',
			'change_document_visibility' => 'The user can change the visibility of the documents',
			'edit_document' => 'The user can edit documents',
			'delete_document' => 'The user can delete documents',
			'import_documents' => 'The user can import documents from folders or external URL',
			'upload_documents' => 'The user can upload documents',
			'make_search' => 'The user can access all the private documents of the institution',
			'manage_own_groups' => 'The user can manage personal document collections',
			'manage_institution_groups' => 'The user can manage institution\'s document collections',
			
			'manage_share' => 'User may share private documents with a single or a group of users',
			'receive_share' => 'User can see documents that have been shared with him',
			
			'manage_share_personal' => 'User may share private documents with a single or a "personal" group of users',
			'manage_share_private' => 'User can share documents to groups of users defined at institution level',
			
			'clean_trash' => 'User can permanently remove documents from the trash',
			
			'manage_personal_people' => 'User can create/edit groups of users defined at personal level',
			'manage_people' => 'User can create/edit groups of users defined at institution level',

		),
		
		'types' => array(

			'guest' => 'Guest',
	        'partner' => 'Partner',
	        'content_manager' => 'Content Manager',
	        'quality_content_manager' => 'Quality Content Manager',
	        'admin' => 'Admin',

		),

		'create' => array(

			'title' => 'Create New Account',
			'slug' => 'Create',

		),

		'created_msg' => 'User created, the password has been sent directly to the users email',
		'edit_disabled_msg' => 'You cannnot modify your account capabilities. Profile configuration can also be made through the <a href=":profile_url">profile page</a>.',
		'disabled_msg' => 'User :name disabled',
		'enabled_msg' => 'User :name back in action',
		'updated_msg' => 'User updated',
		'mail_subject' => 'Your K-Link DMS account is ready',
		'reset_sent' => 'Password reset e-mail sent to :name (:email)',
		'reset_not_sent' => 'The Password reset e-mail cannot be sent to :email. :error',
		'reset_not_sent_invalid_user' => 'The user, :email, cannot be found.',
		'send_reset_password_btn' => 'Password reset',
		'send_reset_password_hint' => 'Request a password link reset for the user',
		'send_message_btn' => 'Send Message',
		'send_message_btn_hint' => 'Send a Message to each user',
	),

	'language' => array(

		'list_label' => 'Here is the list of supported languages.',
		'code_column' => 'Language code',
		'name_column' => 'Language name',

	),

	'storage' => array(

		'disk_status_title' => 'Disk status',
		'documents_report_title' => 'Document Types',
		'disk_number' => 'Disk :number',
		'disk_type_all' => 'Main and Documents Disk',
		'disk_type_main' => 'Main Disk',
		'disk_type_docs' => 'Documents Disk',
		'disk_space' => ':free <strong>free</strong>, :used used of :total total.',

		'reindexall_btn' => 'Reindex all Documents',

		'reindexing_status' => 'Reindexing :number documents...',
		'reindexing_all_status' => 'Reindexing all documents...',
		'reindexing_status_completed' => 'All documents has been reindexed.',

		'naming_policy_title' => 'File Naming Convention',
		'naming_policy_description' => 'You can prevent the upload of files that don\'t follow a particular naming convention',

		'naming_policy_btn_activate' => 'Enable naming convention',
		'naming_policy_btn_save' => 'Update',
		'naming_policy_btn_deactivate' => 'Disable naming convention',

		'naming_policy_msg_activated' => 'Naming convention enabled',
		'naming_policy_msg_deactivated' => 'Naming convention disabled',

	),

	'network' => array(

		'klink_net_title' => 'K-Link Network Connection',

		'net_cards_title' => 'Network interfaces',

		'no_cards' => 'No Network Connections found.',

		'cards_problem' => 'There was a problem extracting network cards information. Here is the detailed developer response',

		'current_ip' => 'Current IP Address :ip',

		'klink_status' => array(
			'success' => 'Established and verified',
			'failed' => 'Cannot connect to the K-Link Core',
		)

	),
	'mail' => array(
		'save_btn' => 'Save Mail configuration',
		'configuration_saved_msg' => 'The Mail configuration has been succesfully saved.',
		'test_success_msg' => 'The test E-Mail has been successfully sent (from :from). Check your inbox.',
		'test_failure_msg' => 'The test email cannot be sent due to an error.',
		'enable_chk' => 'Enable Sending E-Mails',
		'test_btn' => 'Send a test E-Mail',
		'from_label' => 'Global "From" Address',
		'from_description' => 'Here, you may specify a name and address that is used globally for all e-mails that are sent by the K-Link DMS.',
		'from_name_placeholder' => 'Name (e.g. John)',
		'from_address_placeholder' => 'E-Mail (e.g. john@klink.org)',
		'host_label' => 'SMTP Host Address',
		'port_label' => 'SMTP Host Port',
		'encryption_label' => 'E-Mail Encryption Protocol',
		'username_label' => 'SMTP Server Username',
		'password_label' => 'SMTP Server Password',
	),
	'update' => array(),
	'maintenance' => array(

		'queue_runner' => 'Asynchronous process jobs runner',

		'queue_runner_started' => 'Started and listening',
		'queue_runner_stopped' => 'Not running',

		'queue_runner_not_running_description' => 'The jobs runner is not running so Mail Messages and Document Indexing may not work as expected.',
		
		'logs_widget_title' => 'Latest Log entries', 
	),

	'settings' => array(
		'viewing_section' => 'Viewing',
		'viewing_section_help' => 'You can configure how the users can view the documents.',
		'save_btn' => 'Save Settings',
		'saved' => 'Settings has been updated. When the users will refresh the page they will see the update.',
		'save_error' => 'The settings cannot be saved. :error',
		
		'map_visualization_chk' => 'Enable the map visualization',
		
		'klinkpublic_section' => 'K-Link Public',
		'klinkpublic_section_help' => 'Configure the access to the K-Link Public network',
		'klinkpublic_enabled' => 'Enable publish documents to the K-Link Public',
		'klinkpublic_debug_enabled' => 'Enable the Debug of the K-Link Connection',
		'klinkpublic_username' => 'The user used for authenticating on K-Link Public',
		'klinkpublic_password' => 'The password used for authenticating on K-Link Public',
		'klinkpublic_url' => 'The URL of the K-Link Public reference node',
		
		
	),

];
