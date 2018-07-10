<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail messages
    |--------------------------------------------------------------------------
    |
    |
    */

    // Global layout elements
    'logo_text' => 'K-Box',
    'footer_disclaimer' => "You are receiving this E-mail because you are a member of <a href=\":url\">:instance</a>",
    'footer_help' => "<a href=\":url\">Help</a>",

    'welcome' => [

        /*
            Welcome mail template
         */

        'disclaimer' => 'This E-Mail contains your access credentials. Please keep it in a safe place.',
        'welcome' => 'Welcome :name',
        'credentials' => 'you can now access the K-Box of your Institution with<br/>username <strong>:mail</strong><br/>password <strong>:password</strong>',
        'credentials_alt' => 'you can now access the K-Box of your organization with the following credentials: ',
        'username' => 'user **:mail**',
        'password' => 'password `:password`',

        'login_button' => '<a href=":link">Login</a>',
        'login_button_alt' => 'Login',
        

    ],
    
    'password_reset_subject' => 'You requested a password reset for your K-Box account',

    'sharecreated' => [
        /**
         * Strings for the mails.shares.created email template. Used when a share to a document is created
         */
        'subject' => 'K-Box - :user has shared :title with you',
        'shared_document_with_you' => ':user shared a document with you',
        'shared_collection_with_you' => ':user shared a collection with you',
        'title_label' => 'Title',
    ],

    'duplicatesnotification' => [
        'subject' => 'We found some duplicates in your recent uploads',
        'greetings' => 'During the period duplication check, we found that some of your documents are duplicates of existing ones. If you are fine with the duplication ignore this message.',
        'action' => 'See the duplicates from your recent documents',
    ],

];
