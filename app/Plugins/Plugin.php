<?php

namespace KBox\Plugins;

use Illuminate\Support\Facades\Event;
use KBox\Documents\Facades\Thumbnails;
use KBox\Contracts\Plugin as PluginContract;

/**
 * A plugin.
 *
 * Extend this class to create your own plugin.
 *
 * @see \KBox\Contracts\Plugin
 */
abstract class Plugin implements PluginContract
{
    /**
     * The application instance.
     *
     * @var \KBox\Plugins\Application
     */
    protected $app;
    
    /**
     * Create a new Application instance.
     *
     * @param  \KBox\Plugins\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @inheritDoc
     */
    abstract public function boot();

    /**
     * @inheritDoc
     */
    abstract public function register();

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app->make('config')->get($key, []);

        $this->app->make('config')->set($key, array_merge(require $path, $config));
    }

    /**
     * Load the given routes file if routes are not already cached.
     *
     * @param  string  $path
     * @return void
     */
    protected function loadRoutesFrom($path)
    {
        if (! $this->app->routesAreCached()) {
            require $path;
        }
    }

    /**
     * Register a view file namespace.
     *
     * @param  string|array  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadViewsFrom($path, $namespace)
    {
        $this->app->make('view')->addNamespace($namespace, $path);
    }

    /**
     * Register a translation file namespace.
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadTranslationsFrom($path, $namespace)
    {
        $this->app->make('translator')->addNamespace($namespace, $path);
    }

    /**
     * Register a JSON translation file path.
     *
     * @param  string  $path
     * @return void
     */
    protected function loadJsonTranslationsFrom($path)
    {
        $this->app->make('translator')->addJsonPath($path);
    }

    /**
     * Register an event listener
     *
     * @param  string  $event The event class name
     * @param  string  $listener The listener class name
     * @return void
     */
    protected function registerEventListener($event, $listener)
    {
        Event::listen($event, $listener);
    }

    /**
     * Register a thumbnail generator
     *
     * @param  string  $generator The generator class name
     * @return void
     */
    protected function registerThumbnailGenerator($generator)
    {
        Thumbnails::register($generator);
    }
}
