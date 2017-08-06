<?php

namespace Content\Providers;

use Illuminate\Support\ServiceProvider;
use Content\Services\PreviewService;

/**
 * Exposes the services related to the file preview
 * and content extraction
 */
class PreviewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Translation loading and publishing

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'preview');

        $this->publishes([
            __DIR__.'/../../lang' => resource_path('lang/vendor/preview'),
        ]);

        // Views loading and publishing

        $this->loadViewsFrom(__DIR__.'/../../views', 'preview');

        $this->publishes([
            __DIR__.'/../../views' => resource_path('views/vendor/preview'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PreviewService::class, function ($app) {
            return new PreviewService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PreviewService::class];
    }
}
