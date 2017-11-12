<?php

namespace Content\Providers;

use Illuminate\Support\ServiceProvider;
use Content\Services\TextExtractionService;

class TextExtractionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TextExtractionService::class, function ($app) {
            return new TextExtractionService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TextExtractionService::class];
    }
}
