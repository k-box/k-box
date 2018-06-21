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

    "password" => "Passwörter müssen mindestens acht Zeichen lang sein.",
    "user" => "Wir können keinen Nutzer mit dieser Adresse finden.",
    "token" => "Der verwendete Link für das Passwortzurücksetzen ist abgelaufen. Er ist nur für 5 Minuten nach versenden der E-Mail gültig.",
    "sent" => "Passwortlink versendet!",
    "reset" => "Passwort wurde zurückgesetzt!",

    'forgot' => [

        'link' => 'Passwort vergessen?',

        'title' => 'Passwort vergessen?',

        'instructions' => 'Bitte geben Sie ihre E-Mailadresse an, um das Passwort zurückzusetzen. Eine E-Mail mit einem Passwort-Link wird an Ihre E-Mailadresse versendet.',

        'submit' => 'Passwort zurücksetzen',

        'email_subject' => 'K-Box Passwortwiederherstellung',

    ],

    'reset' => [

        'title' => 'Nutzerpasswort zurücksetzen',

        'instructions' => 'Bitte geben sie ein mindestens 8 Zeichen langes Passwort an.',

        'submit' => 'Passwort ändern',

        'email_subject' => 'Ihr K-Box Passwort wurde geändert',

    ],

];
