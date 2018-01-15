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
    'footer_disclaimer' => "Vous recevez cet email parce que vous êtes membre de <a href=\":url\">:instance</a>",
    'footer_help' => "<a href=\":url\">Aide</a>",

    'welcome' => [

        /*
            Welcome mail template
         */

        'disclaimer' => 'Cet email contient vos identifiants de connexion, veuillez les conserver dans un endroit sûr.',
        'welcome' => 'Bienvenue :name',
        'credentials' => 'vous pouvez maintenant accéder à la K-Box de votre institution avec <br/>nom d\'utilisateur <strong>:mail</strong><br/>mot de passe <strong>:password</strong>',
        'credentials_alt' => 'vous pouvez maintenant accéder à la K-Box de votre institution avec les identifiants de connexion suivants',
        'username' => 'nom d\'utilisateur **:mail**',
        'password' => 'mot de passe `:password`',

        'login_button' => '<a href=":link">Connexion</a>',
        'login_button_alt' => 'Connexion',
        

    ],
    
    'password_reset_subject' => 'Vous avez demandé une réinitialisation du mot de passe de votre compte K-Box',

    'sharecreated' => [
        /**
         * Strings for the mails.shares.created email template. Used when a share to a document is created
         */
        'subject' => 'K-Box - :user a partagé :title avec vous',
        'shared_document_with_you' => ':user a partagé un document avec vous',
        'shared_collection_with_you' => ':user a partagé une collection avec vous',
        'title_label' => 'Titre',
    ],

    ];
