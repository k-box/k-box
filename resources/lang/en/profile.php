<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Profile Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the users profile page
    |
    */

    'page_title' => ':name\'s profile',

    'profile' => 'Profile',
    
    'go_to_profile' => 'View your profile',
    
    'messages' => [
        'mail_changed' => 'E-Mail address changed.',
        'name_changed' => 'Username changed.',
        'info_changed' => 'User information changed.',
        'password_changed' => 'Password changed.',
        'language_changed' => 'Language updated.',
    ],

    'errors' => [
        'username_already_taken' => 'The username is already taken. Maybe try a variation of it.',
    ],

    'labels' => [
        'nicename' => 'User\'s Nicename',
        'nicename_hint' => 'How would you like to be called?',
        'password' => 'Password',
        'password_description' => 'The password must have a minimum of 8 characters and can contain numbers, letters and special characters.',
        'password_confirm' => 'Confirm your password',
        'language' => 'Select your preferred language',
        'organization_name' => 'Organization Name',
        'organization_name_hint' => 'The organization you are part of, if any',
        'organization_website' => 'Organization Website',
        'organization_website_hint' => 'The organization website (e.g. https://your-organization.com)',
    ],

    'change_password_btn' => 'Change password',
    'update_profile_btn' => 'Update profile',
    'change_mail_btn' => 'Change E-Mail',
    'change_language_btn' => 'Change Language',

    'info_section' => 'Information',
    'email_section' => 'Change E-Mail',
    'password_section' => 'Change password',
    'language_section' => 'Change Interface Language',

    'starred_count_label' => ':number document starred|:number documents starred',
    'documents_count_label' => ':number document uploaded|:number documents uploaded',
    'collections_count_label' => ':number document collection|:number document collections',
    'shared_count_label' => ':number share created|:number shares created',
    
    'account_settings' => 'Account Settings',

    'privacy' => [
        'privacy' => 'Privacy',
        'section_name' => 'Change your privacy preferences',
        'section_description' => '',

        'activity' => [
            'consent_given' => 'The consent was given by you on :date',
            'consent_withdrawn_by_system' => 'The consent was withdrawn by a privacy policy change on :date',
            'consent_withdrawn_by_user' => 'You withdrawn the consent on :date',
        ],

        'update_privacy_preferences' => 'Update privacy preferences',
    ],

    'export_section' => 'Personal Data Export',

    'data-export' => [
        'hint' => 'Download a copy of your personal data and all data you uploaded to the K-Box. Exports will be generated in zip format and are available for a limited time.',
        'generate' => 'Generate a package with my data',
        'no-exports' => 'You don\'t have pending or old exports. After an export will be ready you can find the details here',
        'triggered' => 'We have received your request to obtain a copy of your data. We will notify you with an email when the export can be downloaded.',
        'wait_until' => 'There are already exports in progress, please wait :minutes minutes before asking for a new export',

        'expired' => '[Expired package] This package is no longer available for download.',
        'pending' => '[Generating package] This package is being generated, you will receive a notification once available.',
        'download' => 'Download package',

        'table' => [
            'export_name' => 'Export request',
            'requested_at' => 'Requested at',
            'available_until' => 'Available until',
        ],
    ],

    'storage' => [
        'menu' => 'Storage',
        'title' => 'Manage storage',

        'big_files_section' => 'Here are the biggest files you have',
        'big_files_section_hint' => 'We found the following files that might have a negative impact over your used quota',

        'view_trash' => 'View trash content',

    ]

];
