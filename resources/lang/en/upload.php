<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used primarly for the video uploader
    |
    */

    'start' => 'Start the upload',
    'remove' => 'Remove upload from the list',
    'open_file_location' => 'Open file location',
    'cancel' => 'Cancel upload',
    'cancel_question' => 'Are you sure you want to cancel this upload?',

    'action_drop' => 'Drop here a file',
    'action_select' => 'Select it',

    'to' => 'to',

    'do_not_leave_the_page' => 'Please do not leave this page during the upload process. You can open a new tab in your browser and keep browsing the K-Box there until the upload finished.',

    'upload_spec_info' => 'Supported videos are mp4 encoded with H.264 codec between 480x360 and 1920x1080 pixels',

    'target' => [
        'personal' => 'to your <a href=":link" target="_blank" rel="noopener noreferrer">private</a> space.',
        'collection' => 'to the collection <a href=":link" target="_blank" rel="noopener noreferrer">:name</a> under <strong>My Collections</strong>.',
        'project' => 'to project <a href=":link" target="_blank" rel="noopener noreferrer">:name</a>.',
        'project_collection' => 'to the collection <a href=":link" target="_blank" rel="noopener noreferrer">:name</a> inside the project <a href=":project_link" target="_blank" rel="noopener noreferrer">:project_name</a>.',
        'error' => 'You cannot access the selected collection.',
    ],

    'status' => [
        'started' => 'started',
        'queued' => 'queued',
        'uploading' => 'uploading',
        'completed' => 'completed',
        'cancelled' => 'cancelled',
        'failed' => 'failed',
    ],
];
