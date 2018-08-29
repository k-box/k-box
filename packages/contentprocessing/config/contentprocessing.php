<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default queue connection for asynchrounous content processing
    |--------------------------------------------------------------------------
    |
    | The queue connection used to dispatch lengthy elaboration processes
    |
    | @var string
    |
    */
    'queue' => env('KBOX_CONTENT_PROCESSING_QUEUE_CONNECTION', 'default'),
];
