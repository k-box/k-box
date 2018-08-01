<?php

namespace Tests\Feature\Plugins;

use Mockery;
use Tests\TestCase;
use KBox\Plugins\PluginManager;
use KBox\Plugins\PluginManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use KBox\Plugins\Application as PluginsApplication;

class PluginManagerTest extends TestCase
{
    public function test_manager_lists_discovered_plugins()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.json';
        
        Storage::fake('app');

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $this->assertEquals(1, $manager->plugins()->count());
        $this->assertEquals(0, $manager->enabled()->count());
        $this->assertFalse($manifest->plugins()->first()->enabled);
    }
    
    public function test_manager_enable_plugin()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.php';
        
        Storage::fake('app');

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $manager->enable('k-box-unittest-demo-plugin');

        Storage::disk('app')->assertExists($enabledPluginManifest);
        $this->assertEquals(1, $manager->enabled()->count());
        $this->assertEquals('k-box-unittest-demo-plugin', $manager->enabled()->first()->name);
        $this->assertTrue($manager->enabled()->first()->enabled);
        $this->assertTrue($manager->plugins()->first()->enabled);
    }
    
    public function test_enabling_non_existent_plugin_does_not_generate_errors()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.php';
        
        Storage::fake('app');

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $manager->enable('a-plugin-name');

        Storage::disk('app')->assertExists($enabledPluginManifest);
        $this->assertEquals(0, $manager->enabled()->count());
    }

    public function test_manager_disable_plugin()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.php';
        
        Storage::fake('app');

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $manager->enable('k-box-unittest-demo-plugin');
        Storage::disk('app')->assertExists($enabledPluginManifest);
        $this->assertEquals(1, $manager->enabled()->count());
        
        $manager->disable('k-box-unittest-demo-plugin');
        Storage::disk('app')->assertExists($enabledPluginManifest);
        $this->assertEquals(0, $manager->enabled()->count());
        $this->assertFalse($manager->plugins()->first()->enabled);
    }

    public function test_manager_register_enabled_plugin()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.php';
        
        Storage::fake('app');

        $app = Mockery::mock(PluginsApplication::class);
        $app->shouldReceive('runningUnitTests')
            ->times(1)
            ->andReturn(true);

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $manager->enable('k-box-unittest-demo-plugin');

        $manager->register($app);
    }

    public function test_manager_boot_enabled_plugin()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.php';
        
        Storage::fake('app');

        $app = Mockery::mock(PluginsApplication::class);
        $app->shouldReceive('runningUnitTests')
            ->times(1)
            ->andReturn(true);

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $manager->enable('k-box-unittest-demo-plugin');

        $manager->boot($app);
    }
}
