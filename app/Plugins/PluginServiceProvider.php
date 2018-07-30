<?php


namespace KBox\Plugins;

use KBox\Contracts\Plugin as PluginContract;

abstract class PluginServiceProvider implements PluginContract
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

    abstract public function boot();

    abstract public function register();
}
