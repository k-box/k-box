<?php

namespace Tests\Feature\Plugins;

use Tests\TestCase;
use KBox\Plugins\Plugin;
use KBox\Events\FileDeleted;
use Illuminate\Support\Facades\Event;
use KBox\Documents\Facades\Thumbnails;
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
    
    public function test_thumbnail_generator_can_be_registered()
    {
        $app = new PluginsApplication(app());
        
        $plugin = new ThumbnailGeneratorRegistrationPlugin($app);
        
        $before = Thumbnails::generators();
        
        $plugin->register();
        
        $after = Thumbnails::generators();
    
        $this->assertEquals(['Class'], array_values(array_diff($after, $before)));
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

class ThumbnailGeneratorRegistrationPlugin extends Plugin
{
    public function register()
    {
        $this->registerThumbnailGenerator('Class');
    }

    public function boot()
    {
    }
}

class FileDeletedEventListenerForPlugin
{
}
