<?php


namespace KBox\Plugins;

/**
 * Wrap the @see Illuminate\Contracts\Foundation\Application for plugins
 */
class Application
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new Application instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
}
