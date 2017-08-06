<?php

namespace Klink\DmsDocuments;

use Illuminate\Support\ServiceProvider;

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
            return new \Klink\DmsDocuments\DocumentsService;
        });
        
        $this->app->singleton(StorageService::class, function ($app) {
            return new StorageService();
        });
        
        $this->app->singleton('Klink\DmsDocuments\FileContentExtractor', function ($app) {
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
        return ['\Klink\DmsDocuments\DocumentsService', '\Klink\DmsDocuments\StorageService', '\Klink\DmsDocuments\FileContentExtractor', '\Klink\DmsDocuments\TrashContentResponse'];
    }
}
