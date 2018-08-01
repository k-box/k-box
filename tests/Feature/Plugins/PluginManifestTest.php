<?php

namespace Tests\Feature\Plugins;

use Tests\TestCase;
use KBox\Plugins\PluginManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class PluginManifestTest extends TestCase
{
    public function test_plugin_manifests_are_loaded()
    {
        $pluginManifest = 'plugins.php';
        
        Storage::fake('app');

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );

        $manifest->build();

        Storage::disk('app')->assertExists($pluginManifest);

        $this->assertEquals(1, $manifest->plugins()->count());
        
        $this->assertEquals('k-box-unittest-demo-plugin', $manifest->plugins()->first()->name);
        $this->assertEquals('Demo K-Box plugin', $manifest->plugins()->first()['description']);
        $this->assertEquals(base_path('/tests/fixtures/plugins/example'), $manifest->plugins()->first()->path);
        $this->assertEquals([[
            "name"=> "Alessio Vertemati",
            "email"=> "alessio@oneofftech.xyz"
        ]], $manifest->plugins()->first()->authors);

        $this->assertEquals(['Tests\Plugins\Example\TestExamplePlugin'], $manifest->providers());
    }
}
