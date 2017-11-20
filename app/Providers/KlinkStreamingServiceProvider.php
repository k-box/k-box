<?php

namespace KlinkDMS\Providers;

use KlinkDMS\Option;
use Illuminate\Support\ServiceProvider;
use Oneofftech\KlinkStreaming\Client as StreamingClient;

class KlinkStreamingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(StreamingClient::class, function ($app) {
            $service_url = Option::option(Option::STREAMING_SERVICE_URL, null);
            $token = @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD));
            $app_url = rtrim(config('app.url'), '/');

            return new StreamingClient($service_url, $token, $app_url);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [StreamingClient::class];
    }
}
