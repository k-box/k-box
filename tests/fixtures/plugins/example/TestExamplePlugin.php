<?php

namespace Tests\Plugins\Example;

use KBox\Plugins\PluginServiceProvider;

class TestExamplePlugin extends PluginServiceProvider
{
    public function register()
    {
        // This is just a call to verify that class
        // is instantiated and method called
        $this->app->runningUnitTests();
    }

    public function boot()
    {
        // This is just a call to verify that class
        // is instantiated and method called
        $this->app->runningUnitTests();
    }
}
