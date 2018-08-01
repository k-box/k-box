<?php

namespace KBox\Plugins;

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
}
