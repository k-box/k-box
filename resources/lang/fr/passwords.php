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

    "password" => "Les mots de passe doivent avoir au moins 6 caractères et doivent correspondre à la confirmation.",
    "user" => "Nous n\'avons pas trouvé d\'utilisateur avec cette adresse email.",
    "token" => "Le lien de réinitialisation du mot de passe que vous avez utilisé a expiré. Il est valide uniquement pendant 5 minutes après la demande de réinitialisation.",
    "sent" => "Le lien de réinitialisation du mot de passe a été envoyé!",
    "reset" => "Le mot de passe a été réinitialisé!",

    'forgot' => [

        'link' => 'Mot de passe oublié?',

        'title' => 'Mot de passe oublié?',

        'instructions' => 'Pour réinitialiser votre mot de passe, veuillez indiquer votre adresse email. Un email avec un lien de réinitialisation vous sera envoyé.',

        'submit' => 'Demander une réinitialisation du mot de passe',

        'email_subject' => 'Demande de réinitialisation du mot de passe K-Box',

    ],

    'reset' => [

        'title' => 'Réinitialisez le mot de passe de votre compte',

        'instructions' => 'Veuillez indiquer un nouveau mot de passe avec minimum 8 caractères alphanumériques.',

        'submit' => 'Réinitialiser le mot de passe',

        'email_subject' => 'Le mot de passe de votre compte K-Box a été modifié',

    ],

    
];
