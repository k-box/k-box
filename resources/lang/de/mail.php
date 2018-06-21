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
    'footer_disclaimer' => "Sie erhalten diese E-Mail, da sie Mitglied von <a href=\":url\">:instance</a> sind",
    'footer_help' => "<a href=\":url\">Hilfe</a>",

    'welcome' => [

        /*
            Welcome mail template
         */

        'disclaimer' => 'Diese E-Mail enthält ihre Zugangsdaten, bitte behandeln Sie sie vertraulich.',
        'welcome' => 'Willkommen :name',
        'credentials' => 'Sie können sich auf der K-Box ihrer Institution nun anmelden.<br/>Nutzername <strong>:mail</strong><br/>Passwort <strong>:password</strong>',
        'credentials_alt' => 'you can now access the K-Box of your organization with the following credentials',
        'username' => 'Nutzername **:mail**',
        'password' => 'Passwort `:password`',

        'login_button' => '<a href=":link">Anmelden</a>',
        'login_button_alt' => 'Anmelden',


    ],

    'password_reset_subject' => 'Passwortzurücksetzung für ihr K-Box Konto',

    'sharecreated' => [
        /**
         * Strings for the mails.shares.created email template. Used when a share to a document is created
         */
        'subject' => 'K-Box - :user hat :title mit ihnen geteilt',
        'shared_document_with_you' => ':user hat ein Dokument mit ihnen geteilt',
        'shared_collection_with_you' => ':user hat eine Sammlung mit ihnen geteilt',
        'title_label' => 'Titel',
    ],

    ];
