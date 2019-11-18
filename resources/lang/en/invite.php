<?php

return [

    'label' => 'Invites',
    'hint' => 'You can invite other users to register an account. Here are listed the invites created by you.',
    'hint_expiration' => 'Invites expire after :period, once expired will be automatically deleted.',

    'create' => [
        'not-authorized-verified-email' => 'You cannot create invites. Please verify that you have a verified email address.',
        'not-authorized' => 'You cannot create invites',

        'title' => 'Invite a person',
        'btn' => 'Invite',
    ],
    
    'created' => '":email" invited to create an account',
    'deleted' => 'Invite to ":email" removed',

    'labels' => [
        'invited_on' => 'Invited on',
        'accepted_on' => 'Accepted on',
        'status' => 'Status',
        
        'remove_invite' => 'Remove invite',

        'empty' => 'No invites to show yet.',
    ],

    'status' => [
        'pending' => 'pending',
        'accepted' => 'accepted',
        'expired' => 'expired',
        'errored' => 'errored',
    ],

    'notification' => [
        'mail' => [
            'subject' => '":name" wants to invite you to use the K-Box',
            'greeting' => 'Hello, ":name" would welcome you to join the K-Box, a web application for managing documents, at :url',
            'no_further_action' => 'If you do not intend to accept this invitation, no further action is required. To respect your privacy we will delete this invitation on :date at 23:59 (UTC).',

            'reason' => [
                'invitation' => 'An existing user, that we know as ":name", wants to invite you to create an account on the K-Box at :url.',
                'project' => 'An existing user, that we know as ":name", wants to add you as part of a project managed with the help of the K-Box at :url.',
                'shared' => 'An existing user, that we know as ":name", wants to share with you a document hosted on the K-Box at :url.',
            ],
        ],
    ],

    'invalid' => 'The used invite is not valid. It might be expired or revoked.',
    
    'registration-label' => 'Invite',
];
