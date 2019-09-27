<?php

return [
    'create' => [
        'not-authorized' => 'You cannot create invites. Please verify that you have a verified email address',
    ],

    'notification' => [
        'mail' => [
            'subject' => '":name" wants to invite you to use the K-Box',
            'greeting' => 'Hello, ":name" would welcome you to join the K-Box, a web application for managing documents, at :url',
            'no_further_action' => 'If you do not intend to accept this invitation, no further action is required. To respect your privacy we will delete this invitation after :period.',

            'reason' => [
                'invitation' => 'An existing user, that we know as ":name", want to invite you to create an account on the K-Box at :url.',
                'project' => 'An existing user, that we know as ":name", want to add you as part of a project managed with the help of the K-Box at :url.',
                'shared' => 'An existing user, that we know as ":name", want to share with you a document hosted on the K-Box at :url.',
            ],
        ],
    ],
];
