<?php


namespace KBox\Contracts;

interface Plugin
{
    /**
     * Register method, called when the plugin is first registered.
     *
     * This is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register();
    
    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot();
}
