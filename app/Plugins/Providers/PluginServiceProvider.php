<?php

namespace KBox\Plugins\Providers;

use Illuminate\Support\ServiceProvider;
use KBox\Plugins\PluginManager;
use KBox\Plugins\PluginManifest;
use Illuminate\Filesystem\Filesystem;
use KBox\Plugins\Console\PluginDiscoverCommand;
use KBox\Plugins\Application as PluginsApplication;

class PluginServiceProvider extends ServiceProvider
{
    private $pluginManifest;

    private $pluginManager;

    private $pluginApplication;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->pluginManifest = new PluginManifest(
            new Filesystem,
            $this->app->basePath().'/plugins/',
            storage_path('app/plugins.php')
        );

        $this->pluginManager = new PluginManager(
            $this->pluginManifest,
            new Filesystem,
            storage_path('app/enabled-plugins.php')
        );

        $this->pluginApplication = new PluginsApplication($app);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoleCommands();

        $this->pluginManager->boot($this->pluginApplication);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PluginManager::class, function ($app) {
            return $this->pluginManager;
        });

        $this->app->bind(PluginManifest::class, function ($app) {
            return $this->pluginManifest;
        });

        $this->app->singleton(PluginsApplication::class, function ($app) {
            return $this->pluginApplication;
        });

        $this->pluginManager->register($this->pluginApplication);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function registerConsoleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PluginDiscoverCommand::class,
            ]);
        }
    }
}
