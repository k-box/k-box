<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messaging Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the admin to users communications
    |
    */

    'create_pagetitle' => 'Nachricht verfassen...',

    'message_sent' => 'Die Nachricht wurde gesendet.',
    'message_error' => 'Fehler beim senden der Nachricht. :error',
    'error_empty_users' => 'Bitte zumindest einen Nutzer auswählen.',
    'error_users_not_found' => 'Die folgenden Empfänger wurden nicht gefunden: :users',

    'labels' => [
        'users' => 'Nutzer, die die Nachricht erhalten sollen',
        'text' => 'Nachrichtentext',
        'submit_btn' => 'Nachricht Senden',
    ],

    'mail' => [

        'intro' => 'Hallo :name,', # Gender neutral

        'subject' => 'Nachricht von der K-Box',

        'signature' => ':name<br/>gesendet von der K-Box.',

        'you_are_receiving_because' => 'Sie erhalten diese E-Mail, da sie Nutzer der <a href=":link">K-Box</a> sind.',
        
        'do_not_reply' => 'Dies ist eine automatische Nachricht. Bitte nicht antworten', 

    ],

];
