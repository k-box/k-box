<?php

namespace KBox\Plugins\Console;

use KBox\Plugins\PluginManifest;
use Illuminate\Console\Command;

class PluginDiscoverCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild plugin cache.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PluginManifest $manifest)
    {
        $manifest->build();

        foreach (array_keys($manifest->manifest) as $plugin) {
            $this->line("Discovered Plugin: <info>{$plugin}</info>");
        }

        $this->info('Plugin manifest generated successfully.');
    }
}
