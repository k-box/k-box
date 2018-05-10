<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrator Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for rendering particular messages
    | to the user
    |
    */
    
    'mail_testing_mode_msg' => 'The email configuration is not valid. No mail message will be sent to new users or existing ones. <a href=":url">Change it</a>',
    'mail_not_configured' => 'The E-Mail configuration requires your attention.<br/><a href=":url">Please review the E-Mail settings</a>.',
    'mail_config_msg' => 'Please complete the <a href=":url">E-Mail service configuration</a>.',
    'account_mail_msg' => 'Please <a href=":url">change your account E-Mail</a> to a real E-Mail address, otherwise you will not be able to receive messages.',

    'long_running_msg' => '<strong>Still Working!</strong> The action takes longer this time...',
    
    'terms_of_use' => 'By uploading or sharing a document you agree to the <a href=":policy_link">Service Policy</a>',

    'contacts_not_configured' => 'The Contact information requires your attention. <a href=":url">Please review them</a> from the Identity section.',
    
    'default_license_not_set' => 'The default license for new uploads is not configured. <a href=":url">Please select a default license</a>.',
    'available_licenses_not_set' => 'The list of usable licenses is not configured. <a href=":url">Please review the list of available licenses</a>.',
    
    'license_configuration_error' => '<strong>This installation lacks the obligatory copyright information about "All rights reserved". Please contact your system administrator</strong>.<br/>This will impact on search and network publication.',
    
    // general upload blocked message for users
    'uploads_blocked' => 'The K-Box is currently in read-only mode. New file uploads are not permitted due to maintenance operations.',
    
    // upload blocked message for administrators
    'uploads_blocked_admin' => 'Read-only mode is active. File uploads are blocked. Please review the configuration.',
];
