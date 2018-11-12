<?php

namespace KBox\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use KBox\Events\ShareCreated;
use KBox\Events\PageChanged;
use KBox\Events\UploadCompleted;
use KBox\Events\PrivacyPolicyUpdated;
use KBox\Listeners\ShareCreatedHandler;
use KBox\Events\FileDuplicateFoundEvent;
use KBox\Listeners\UploadCompletedHandler;
use KBox\Listeners\TusUploadStartedHandler;
use KBox\Listeners\TusUploadCompletedHandler;
use KBox\Listeners\TusUploadCancelledHandler;
use KBox\Listeners\TransformPageToPolicyEvent;
use OneOffTech\TusUpload\Events\TusUploadStarted;
use OneOffTech\TusUpload\Events\TusUploadCompleted;
use OneOffTech\TusUpload\Events\TusUploadCancelled;
use KBox\Notifications\DuplicateDocumentsNotification;
use KBox\Listeners\RemovePrivacyPolicyConsentFromUsers;

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
        FileDuplicateFoundEvent::class => [
            DuplicateDocumentsNotification::class
        ],
        PageChanged::class => [
            TransformPageToPolicyEvent::class
        ],
        PrivacyPolicyUpdated::class => [
            RemovePrivacyPolicyConsentFromUsers::class
        ]
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
