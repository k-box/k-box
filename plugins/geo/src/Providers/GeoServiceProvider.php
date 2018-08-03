<?php

namespace KBox\Geo\Providers;

use KBox\Plugins\Plugin;
use Illuminate\Support\Facades\Route;

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
        // $this->mergeConfigFrom(
        //     __DIR__.'/../../config/geo.php',
        //     'geo'
        // );

        $this->app->singleton(GeoService::class, function ($app) {
            return new GeoService();
            // config('licenses')
        });
    }

}
