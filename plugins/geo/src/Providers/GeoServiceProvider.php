<?php

namespace KBox\Geo\Providers;

use KBox\Plugins\Plugin;
use Illuminate\Support\Facades\Route;
use KBox\Geo\Actions\SyncWithGeoserver;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;

class GeoServiceProvider extends Plugin
{
    /**
     * Bootstrap the plugin services.
     *
     * @return void
     */
    public function boot()
    {

        if (! $this->app->routesAreCached()) {

            Route::middleware('web')
                ->namespace('KBox\Geo\Http\Controllers')
                ->prefix('geoplugin')
                ->as('plugins.k-box-kbox-plugin-geo.')
                ->group(__DIR__.'/../../routes/routes.php');
        }

        // Translation loading
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'geo');

        // Views loading
        $this->loadViewsFrom(__DIR__.'/../../views', 'geo');
    }

    /**
     * Register the plugin offered services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GeoService::class, function ($app) {
            return new GeoService();
        });
        
        // register the custom step in the elaboration pipeline
        DocumentElaboration::register(SyncWithGeoserver::class);
    }

}
