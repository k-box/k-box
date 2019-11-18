<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default elaboration pipeline
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the pipelines below you wish to
    | use as your default pipeline for all document elaborations
    |
    */
    'pipeline' => env('KBOX_DOCUMENT_ELABORATION_PIPELINE', 'default'),
    
    /*
    |--------------------------------------------------------------------------
    | The Document Elaboration pipelines
    |--------------------------------------------------------------------------
    |
    | A pipeline contains the default actions that will be executed as part
    | of the document elaboration. The actions will be run in order.
    | Each action must be a subclass of KBox\Contracts\Action
    |
    | @var array
    |
    */
    'pipelines' => [
        
        'default' => [
            \KBox\DocumentsElaboration\Actions\ExtractFileProperties::class,
            \KBox\DocumentsElaboration\Actions\GuessLanguage::class,
            \KBox\DocumentsElaboration\Actions\AddToSearch::class,
            \KBox\DocumentsElaboration\Actions\EnsureCorrectPictureOrientation::class,
            \KBox\DocumentsElaboration\Actions\GenerateThumbnail::class,
            \KBox\DocumentsElaboration\Actions\ElaborateVideo::class,
        ],
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Default queue connection for the elaboration
    |--------------------------------------------------------------------------
    |
    | The queue connection used to dispatch the elaboration pipeline
    |
    | @var string
    |
    */
    'queue' => env('KBOX_DOCUMENT_ELABORATION_QUEUE_CONNECTION', 'default'),

];
