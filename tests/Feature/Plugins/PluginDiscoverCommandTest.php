<?php

namespace Tests\Feature\Plugins;

use Tests\TestCase;
use KBox\Plugins\PluginManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use KBox\Plugins\Console\PluginDiscoverCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Klink\DmsAdapter\Traits\SwapInstance;

class PluginDiscoverCommandTest extends TestCase
{
    use SwapInstance;

    public function test_plugins_are_discovered()
    {
        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.json';
        
        Storage::fake('app');

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );

        $this->swap(PluginManifest::class, $manifest);

        $command = new PluginDiscoverCommand();
        $command->setLaravel(app());
        
        $output = new BufferedOutput;

        $command->run(new ArrayInput([]), $output);

        Storage::disk('app')->assertExists($pluginManifest);
        $this->assertTrue(str_contains($output->fetch(), "Discovered Plugin: k-box-unittest-demo-plugin"));
    }
}
