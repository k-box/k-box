<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reminder Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    "password" => "Passwords must be at least 8 characters and match the confirmation.",
    "user" => "We can't find a user with that e-mail address.",
    "token" => "The password reset link you have used is expired. It will be usable only for 5 minutes after the password reset request.",
    "sent" => "If the specified email is registered, you should receive an email within a few minutes.",
    'throttled' => 'Please wait before retrying.',
    "reset" => "Password has been reset",

    'forgot' => [

        'link' => 'Forgot your password?',

        'title' => 'Forgot your password?',

        'instructions' => 'To reset your password, please specify your e-mail address. A mail with a reset link will be sent shortly.',

        'submit' => 'Request a Password Reset',

        'email_subject' => 'K-Box Password Reset Request',

    ],

    'reset' => [

        'title' => 'Reset your account password',

        'instructions' => 'Please specify the e-mail address of the account and a new 8 character long password.',

        'submit' => 'Reset the password',

        'email_subject' => 'Your K-Box Account Password has been changed',

    ],

    
];
