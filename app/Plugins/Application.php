<?php


namespace KBox\Plugins;

/**
 * Wrap the @see Illuminate\Contracts\Foundation\Application
 *
 * The main purpose is to let plugins extends the K-Box with clear actions and helpers
 *
 * It is passed to plugins during register and boot actions.
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

    /**
     * Dynamically call the Laravel application instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->app->$method(...$parameters);
    }
}
