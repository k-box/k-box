<?php

namespace KBox\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use KBox\Events\ShareCreated;
use KBox\Events\UploadCompleted;
use KBox\Listeners\ShareCreatedHandler;
use KBox\Listeners\UploadCompletedHandler;
use KBox\Listeners\TusUploadStartedHandler;
use Avvertix\TusUpload\Events\TusUploadStarted;
use Avvertix\TusUpload\Events\TusUploadCompleted;
use Avvertix\TusUpload\Events\TusUploadCancelled;
use KBox\Listeners\TusUploadCompletedHandler;
use KBox\Listeners\TusUploadCancelledHandler;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ShareCreated::class => [
            ShareCreatedHandler::class,
        ],
        TusUploadStarted::class => [
            TusUploadStartedHandler::class,
        ],
        TusUploadCompleted::class => [
            TusUploadCompletedHandler::class,
        ],
        TusUploadCancelled::class => [
            TusUploadCancelledHandler::class,
        ],
        UploadCompleted::class => [
            UploadCompletedHandler::class,
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
