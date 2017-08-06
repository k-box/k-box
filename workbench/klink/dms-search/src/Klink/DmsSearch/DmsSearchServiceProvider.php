<?php

namespace Klink\DmsSearch;

use Illuminate\Support\ServiceProvider;

class DmsSearchServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // $this->package('klink/dms-search');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('searchservice', function ($app) {
            return new \Klink\DmsSearch\SearchService;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['\Klink\DmsSearch\SearchService', '\Klink\DmsSearch\SearchRequest', '\Klink\DmsSearch\EnhancedSearchResults'];
    }
}
