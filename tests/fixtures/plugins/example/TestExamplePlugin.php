<?php

namespace Tests\Plugins\Example;

use KBox\Plugins\Plugin;

class TestExamplePlugin extends Plugin
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
