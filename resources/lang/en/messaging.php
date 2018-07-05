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

    'create_pagetitle' => 'Create new Message to...',
    
    'message_sent' => 'The message has been sent.',
    'message_error' => 'There was an error sending the message. :error',
    'error_empty_users' => 'Please select at least one user.',
    'error_users_not_found' => 'The following recipients cannot be found: :users',

    'labels' => [
        'users' => 'Select the users that will receive the message',
        'text' => 'Insert the text of the message',
        'submit_btn' => 'Send Message',
    ],

    'mail' => [

        'intro' => 'Dear :name,',
        
        'subject' => 'Message from K-Box',
        
        'signature' => ':name<br/>Sent from K-Box.',
        
        'you_are_receiving_because' => 'You are receiving this e-mail because you are a user of the <a href=":link">K-Box</a>.',
        
        'do_not_reply' => 'This is an automatically generated message. Do not reply.',
        
    ],

];
