<?php


namespace KBox\Plugins;

use Exception;
use Illuminate\Filesystem\Filesystem;
use KBox\Plugins\Application as PluginsApplication;

final class PluginManager
{
    private $pluginManifest;
    
    private $plugins = null;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;

    /**
     * The path to the file that contains the enabled plugins configuration.
     *
     * @var string
     */
    private $manifestPath;

    private $manifest = null;

    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $basePath
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(PluginManifest $plugins, Filesystem $files, $enabledPluginsManifest)
    {
        $this->pluginManifest = $plugins;
        $this->files = $files;
        $this->manifestPath = $enabledPluginsManifest;
    }

    /**
     * The list of registered plugins
     */
    public function plugins()
    {
        if (! is_null($this->plugins)) {
            return $this->plugins;
        }
        
        return $this->plugins = $this->pluginManifest->plugins()->merge($this->getManifest());
    }
    
    /**
     * The list of the enabled plugins
     */
    public function enabled()
    {
        return $this->getManifest();
    }

    /**
     * Enable a plugin
     *
     * @param string $plugin The name of the plugin to enable
     */
    public function enable($plugin)
    {
        $instance = $this->plugins()->get($plugin);

        if ($instance && ! $instance->enabled) {
            $instance->enabled = true;

            $this->manifest->put($plugin, $instance);
        }

        $this->write();
    }
    
    /**
     * Disable a plugin
     *
     * @param string $plugin The name of the plugin to disable
     */
    public function disable($plugin)
    {
        $instance = $this->plugins()->get($plugin);

        if ($instance && $instance->enabled) {
            $instance->enabled = false;

            $this->manifest->pull($plugin);
        }

        $this->write();
    }
    
    /**
     * Get a configuration parameter of the plugin
     *
     * @param string $plugin The name of the plugin to get the configuration
     * @param string $key The configuration key to retrieve
     * @param mixed $default The value to return in case the configuration key do not exists. Default null
     * @return mixed The configuration value
     */
    public function config($plugin, $key, $default = null)
    {
    }

    /**
     * Register all enabled Plugins service providers
     *
     * @param PluginsApplication $app
     */
    public function register(PluginsApplication $app)
    {
        $this->enabled()->each(function ($plugin) use ($app) {
            $this->registerPlugin($plugin, $app);
        });
    }

    /**
     * Boot all enabled Plugins
     *
     * @param PluginsApplication $app
     */
    public function boot(PluginsApplication $app)
    {
        $this->enabled()->each(function ($plugin) use ($app) {
            $this->bootPlugin($plugin, $app);
        });
    }

    private function registerPlugin($plugin, $app)
    {
        $provider = $plugin->providers[0];

        (new $provider($app))->register();
    }

    private function bootPlugin($plugin, $app)
    {
        $provider = $plugin->providers[0];

        (new $provider($app))->boot();
    }

    /**
     * Get the current package manifest.
     *
     * @return array
     */
    protected function getManifest()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (! file_exists($this->manifestPath)) {
            return $this->manifest = collect();
        }

        $temp =  file_exists($this->manifestPath) ?
                    $this->files->getRequire($this->manifestPath) : [];

        return $this->manifest = collect($temp)->filter()->map(function ($manifest) {
            return new Manifest($manifest);
        });
    }

    /**
     * Write the given manifest array to disk.
     *
     * @param  array  $manifest
     * @return void
     * @throws \Exception
     */
    protected function write()
    {
        if (! $this->manifest) {
            return;
        }
        $manifest = $this->manifest->toArray();

        if (! is_writable(dirname($this->manifestPath))) {
            throw new Exception('The '.dirname($this->manifestPath).' directory must be present and writable.');
        }

        $this->files->put(
            $this->manifestPath,
            '<?php return '.var_export($manifest, true).';'
        );
    }
}
