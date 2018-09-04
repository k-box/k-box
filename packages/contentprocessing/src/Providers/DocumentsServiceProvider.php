<?php

namespace KBox\Documents\Providers;

use Illuminate\Support\ServiceProvider;
use KBox\Documents\FileContentExtractor;
use KBox\Documents\Services\FileService;
use KBox\Documents\Services\DocumentsService;

class DocumentsServiceProvider extends ServiceProvider
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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/contentprocessing.php',
            'contentprocessing'
        );
        
        $this->app->singleton('documentsservice', function ($app) {
            return new DocumentsService();
        });
        
        $this->app->singleton(StorageService::class, function ($app) {
            return new StorageService();
        });
        
        $this->app->singleton(FileService::class, function ($app) {
            return new FileService();
        });
        
        $this->app->singleton(FileContentExtractor::class, function ($app) {
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
        return [
            '\KBox\Documents\Services\DocumentsService',
            '\KBox\Documents\Services\StorageService',
            '\KBox\Documents\Services\TextExtractionService',
            '\KBox\Documents\Services\PreviewService',
            '\KBox\Documents\Services\ThumbnailService',
            '\KBox\Documents\Services\FileService',
            '\KBox\Documents\FileContentExtractor',
            '\KBox\Documents\TrashContentResponse'];
    }
}
