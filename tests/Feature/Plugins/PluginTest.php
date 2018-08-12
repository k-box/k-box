<?php

namespace Tests\Feature\Plugins;

use Tests\TestCase;
use KBox\Plugins\Plugin;
use KBox\Events\FileDeleted;
use Illuminate\Support\Facades\Event;
use KBox\Plugins\Application as PluginsApplication;

class PluginTest extends TestCase
{
    public function test_event_listeners_are_registered()
    {
        $app = new PluginsApplication(app());
        $plugin = new EventListenerRegistrationPlugin($app);
        
        $before = Event::getListeners(FileDeleted::class);
        
        $plugin->register();
        
        $after = Event::getListeners(FileDeleted::class);

        $this->assertEmpty($before);
        $this->assertNotEmpty($after);
    }
}

class EventListenerRegistrationPlugin extends Plugin
{
    public function register()
    {
        $this->registerEventListener(FileDeleted::class, FileDeletedEventListenerForPlugin::class);
    }

    public function boot()
    {
    }
}

class FileDeletedEventListenerForPlugin
{
}
