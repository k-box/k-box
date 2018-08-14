<?php

namespace KBox\Documents\Providers;

use Illuminate\Support\ServiceProvider;
use KBox\Documents\FileContentExtractor;

class DmsDocumentsServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // $this->package('klink/dms-documents');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('documentsservice', function ($app) {
            return new \KBox\Documents\Services\DocumentsService;
        });
        
        $this->app->singleton(StorageService::class, function ($app) {
            return new StorageService();
        });
        
        $this->app->singleton('KBox\Documents\FileContentExtractor', function ($app) {
            return new FileContentExtractor();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['\KBox\Documents\Services\DocumentsService', '\KBox\Documents\Services\StorageService', '\KBox\Documents\FileContentExtractor', '\KBox\Documents\TrashContentResponse'];
    }
}
