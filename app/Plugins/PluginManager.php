<?php

namespace KBox\Plugins;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use KBox\Plugins\Application as PluginsApplication;

/**
 * The Manager of the plugins
 *
 * Enable, Disable and manage the persistence of the plugin status.
 * It also register and boot enabled plugins
 */
final class PluginManager
{
    /**
     * The instance of the discovered plugin manifest loader
     *
     * @var \KBox\Plugins\PluginManifest
     */
    private $pluginManifest;
    
    /**
     * The list of plugins
     *
     * @var \KBox\Plugins\Manifest[]
     */
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

    /**
     * The list of enabled plugins
     *
     * @var \KBox\Plugins\Manifest[]
     */
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
     *
     * @return KBox\Plugins\Manifest[]
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
     *
     * @return KBox\Plugins\Manifest[]
     */
    public function enabled()
    {
        return $this->getManifest();
    }

    /**
     * Enable a plugin
     *
     * @param string $plugin The name of the plugin to enable
     * @return void
     */
    public function enable($plugin)
    {
        $instance = $this->plugins()->get($plugin);

        if ($instance && ! $instance->enabled) {
            $instance->enabled = true;

            $this->manifest->put($plugin, $instance);
        }

        $this->write();

        // re-cache the routes as the plugin
        // might define custom routes
        if (! app()->runningUnitTests()) {
            Artisan::call('route:cache');
        }
    }
    
    /**
     * Disable a plugin
     *
     * @param string $plugin The name of the plugin to disable
     * @return void
     */
    public function disable($plugin)
    {
        $instance = $this->plugins()->get($plugin);

        if ($instance && $instance->enabled) {
            $instance->enabled = false;

            $this->manifest->pull($plugin);
        }

        $this->write();

        // re-cache the routes as the plugin
        // might have defined custom routes
        if (! app()->runningUnitTests()) {
            Artisan::call('route:cache');
        }
    }
    
    /**
     * Get / set the specified configuration value for a plugin.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param string $plugin The name of the plugin to get the configuration
     * @param string|array|null $key The configuration key to retrieve. Default null, retrieves all configuration keys for the given plugin
     * @param mixed $default The value to return in case the configuration key do not exists. Default null
     * @return mixed The configuration value
     */
    public function config($plugin, $key = null, $default = null)
    {
        $instance = $this->plugins()->get($plugin);
        
        $defaultConfiguration = config($plugin) ?? [];

        $savedConfiguration = $instance['configuration'] ?? [];

        $configuration = array_merge($defaultConfiguration, $savedConfiguration);

        if (is_null($key)) {
            return $configuration;
        }
            
        if (is_array($key)) {
            $instance['configuration'] = array_merge($configuration, $key);
            $this->write();
            return $instance['configuration'];
        }
        
        if (isset($configuration[$key])) {
            return $configuration[$key];
        }

        return $default;
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

    /**
     * @param \KBox\Plugins\Manifest $plugin
     * @param \KBox\Plugins\Application $app
     */
    private function registerPlugin($plugin, $app)
    {
        $provider = $plugin->providers[0];

        (new $provider($app))->register();
    }

    /**
     * @param \KBox\Plugins\Manifest $plugin
     * @param \KBox\Plugins\Application $app
     */
    private function bootPlugin($plugin, $app)
    {
        $provider = $plugin->providers[0];

        (new $provider($app))->boot();
    }

    /**
     * Get the current enabled plugin manifest.
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
