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

    'menu' => [

        'accounts'=>'Accounts',
        'language'=>'Language',
        'storage'=>'Storage',
        'network'=>'Network',
        'mail'=>'Mail',
        'update'=>'Update and recovery',
        'maintenance'=>'Maintenance and Events',
        'institutions'=>'Institutions',
        'settings'=>'Settings',
        'identity' => 'Identity',

    ],

    'accounts' => [

        'disable_confirm' => 'Do you really want to disable :name',

        'create_user_btn' => 'Create User',

        'table' => [

            'name_column' => 'name',
            'email_column' => 'email',
            'institution_column' => 'institution',

        ],
        
        'edit_account_title' => 'Edit :name',

        'labels' => [

            'email' => 'Mail',
            'username' => 'Username',
            'perms' => 'Permissions',

            'cancel' => 'Cancel',

            'create' => 'Create',
            'update' => 'Update',

            'institution' => 'Institution',
            'select_institution' => 'Select the user Institution affiliation...',

        ],

        'capabilities' => [

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
            'manage_project_collections' => 'The user can manage project collections',
            
            'manage_share' => 'User may share private documents with a single or a group of users',
            'receive_share' => 'User can see documents that have been shared with him',
        
            'manage_share_personal' => 'User may share personal documents with a single or a "personal" group of users',
            'manage_share_private' => 'User can share documents to groups of users defined at institution level',
            
            'clean_trash' => 'User can permanently remove documents from the trash',
            
            'manage_personal_people' => 'User can create/edit groups of users defined at personal level',
            'manage_people' => 'User can create/edit groups of users defined at institution level',

        ],
        
        'types' => [

            'guest' => 'Guest',
            'partner' => 'Partner',
            'content_manager' => 'Content Manager',
            'quality_content_manager' => 'Quality Content Manager',
            'project_admin' => 'Project Administrator',
            'admin' => 'K-Box Admin',
            'klinker' => 'K-Linker',

        ],

        'create' => [

            'title' => 'Create New Account',
            'slug' => 'Create',

        ],

        'created_msg' => 'User created, the password has been sent directly to the users email',
        'edit_disabled_msg' => 'You cannnot modify your account capabilities. Profile configuration can also be made through the <a href=":profile_url">profile page</a>.',
        'disabled_msg' => 'User :name disabled',
        'enabled_msg' => 'User :name back in action',
        'updated_msg' => 'User updated',
        'mail_subject' => 'Your K-Box account is ready',
        'reset_sent' => 'Password reset e-mail sent to :name (:email)',
        'reset_not_sent' => 'The Password reset e-mail cannot be sent to :email. :error',
        'reset_not_sent_invalid_user' => 'The user, :email, cannot be found.',
        'send_reset_password_btn' => 'Password reset',
        'send_reset_password_hint' => 'Request a password link reset for the user',
        'send_message_btn' => 'Send Message',
        'send_message_btn_hint' => 'Send a Message to each user',
    ],

    'language' => [

        'list_label' => 'Here is the list of supported languages.',
        'code_column' => 'Language code',
        'name_column' => 'Language name',

    ],

    'storage' => [

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

    ],

    'network' => [

        'klink_net_title' => 'K-Link Network Connection',

        'net_cards_title' => 'Network interfaces',

        'no_cards' => 'No Network Connections found.',

        'cards_problem' => 'There was a problem extracting network cards information. Here is the detailed developer response',

        'current_ip' => 'Current IP Address :ip',

        'klink_status' => [
            'success' => 'Established and verified',
            'failed' => 'Cannot connect to the K-Link Core',
        ]

    ],
    'mail' => [
        'save_btn' => 'Save Mail configuration',
        'configuration_saved_msg' => 'The Mail configuration has been succesfully saved.',
        'test_success_msg' => 'The test E-Mail has been successfully queued for sending (from :from). Check your inbox.',
        'test_failure_msg' => 'The test email cannot be sent due to an error.',
        'enable_chk' => 'Enable Sending E-Mails',
        'enabled' => 'The DMS can send emails',
        'enabled_by_configuration' => 'Send email is enabled by deployment configuration',
        'disabled' => 'The DMS cannot send emails',
        'test_btn' => 'Send a test E-Mail',
        'from_label' => 'Send E-Mail From',
        'from_description' => 'Here, you may specify a name and address that is used globally for all e-mails that are sent by the DMS.',
        'server_configuration_label' => 'Server configuration',
        'server_configuration_description' => 'How the DMS connects to the email server',
        'from_name' => 'Name (e.g John)',
        'from_address' => 'E-Mail address (e.g. john@klink.org)',
        'from_name_placeholder' => 'John',
        'from_address_placeholder' => 'e.g. john@klink.asia',
        'host_label' => 'SMTP Host Address',
        'port_label' => 'SMTP Host Port',
        'encryption_label' => 'The E-Mail server must support TLS Encryption',
        'username_label' => 'SMTP Server Username',
        'password_label' => 'SMTP Server Password',
        'log_driver_used' => 'The log driver is used. You cannot change the server configuration.',
        'log_driver_go_to_log' => 'The email messages will be written in the DMS log file. You can check it from <a href=":link">Administration > Maintenance and Events</a>.',
    ],
    'update' => [],
    'maintenance' => [

        'queue_runner' => 'Asynchronous process jobs runner',

        'queue_runner_started' => 'Started and listening',
        'queue_runner_stopped' => 'Not running',

        'queue_runner_not_running_description' => 'The jobs runner is not running so Mail Messages and Document Indexing may not work as expected.',
        
        'logs_widget_title' => 'Latest Log entries',
    ],
    
    
    'institutions' => [
        
        'edit_title' => 'Edit :name details',
        'create_title' => 'Create new Institution',
        'create_institutions_btn' => 'Add new Institution',
        'saved' => 'Institution :name updated.',
        'update_error' => 'Institution detail not saved: :error',
        'create_error' => 'The Institution cannot be created: :error',
        'delete_not_possible' => 'The institution :name is currently being used for documents and/or user affiliation. Please remove all the documents and the user affiliation before deleting it.',
        'delete_error' => 'The institution :name cannot be deleted: :error',
        'deleted' => 'The institution :name has been removed.',
        'delete_confirm' => 'Deleting institution :name from the network?',
        'deprecated' => 'Institutions management is going to change. To prepare your K-Box to support the next changes we are disabling adding, editing and removing Institutions.',
        
        'labels' => [
            'klink_id' => 'Institution Identifier (in the K-Link Network)',
            'name' => 'Institution name',
            'email' => 'Institution E-Mail for getting information',
            'phone' => 'Institution secretary phone number',
            'url' => 'Institution website address',
            'thumbnail_url' => 'Institution image or avatar (url of an image)',
            'address_street' => 'Institution Street Address',
            'address_country' => 'Institution Country',
            'address_locality' => 'Institution City',
            'address_zip' => 'Postal Code',
            'update' => 'Save Details',
            'create' => 'Create Institution'
        ],
    ],
    
    'settings' => [
        'viewing_section' => 'Viewing',
        'viewing_section_help' => 'You can configure how the users can view the documents.',
        'save_btn' => 'Save Settings',
        'saved' => 'Settings has been updated. When the users will refresh the page they will see the update.',
        'save_error' => 'The settings cannot be saved. :error',
        
        'map_visualization_chk' => 'Enable the map visualization',
        
        'support_section' => 'Support',
        'support_section_help' => 'If you have a support subscription please insert here the authentication token to enable your users to submit support requests and receive help from the K-Link Dev team.',
        'support_token_field' => 'Support Token',
        'support_save_btn' => 'Save Support Settings',

        'analytics_section' => 'Analytics',
        'analytics_section_help' => 'Analytics gives you the power to understand how users are using the system and how many of them use it regularly. In this section you can opt-in for the K-Link Analytics.',
        'analytics_token_field' => 'Analytics token',
        'analytics_save_btn' => 'Save Analytics Settings',
        
    ],

    'identity' => [
        'page_title' => 'Identity',
        'description' => 'Your organization information, so users can contact you from the "Contacts page".',
        'not_complete' => 'Contact information are not complete.',
        'suggestion_based_on_institution_hint' => 'We automatically populated the contact information based on the K-Link Institution information. Please review them and press save.',

        'contact_info_updated' => 'Contact details saved.',
        'update_error' => 'Contact details were not updated. :error',
    ],

];
