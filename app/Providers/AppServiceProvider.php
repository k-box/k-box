<?php

namespace KBox\Providers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Check if a non empty input value is NOT an array.
         * No additional parameters are supported
         */
        Validator::extend('not_array', function ($attribute, $value, $parameters, $validator) {
            return ! is_array($value);
        });

        /**
         * Register a request macro that checks if the request comes from a K-Link that want to download the document
         */
        Request::macro('isKlinkRequest', function () {
            
            // This is a way of identifying that the request is coming from the K-Search, as, thanks to the proxy,
            // the real host and IP addresses are not available
            return $this->isMethod('get')
                   && network_enabled()
                   && str_contains(strtolower($this->userAgent()), 'guzzlehttp');
        });

        /**
         * Register a response macro that respond as an head request
         */
        Response::macro('head', function ($headers) {
            return response('', 200, array_merge(['Content-Length' => 0], $headers));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // This service provider is a great spot to register your various container
        // bindings with the application. As you can see, we are registering our
        // "Registrar" implementation here. You can add your own bindings too!

        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            \KBox\Services\Registrar::class
        );

        // Loading workbench service provider only if running in console (aka Artisan) and the environment is set to 'development'

        if ($this->app->runningInConsole() && $this->app->environment('development')) {
            $this->app->register('Illuminate\Workbench\WorkbenchServiceProvider');
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
