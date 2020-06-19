<?php

use Illuminate\Support\Str;
use KBox\Plugins\PluginManager;
use KBox\Services\ReadonlyMode;
use KBox\Documents\Services\DocumentsService;

if (! function_exists('css_asset')) {
    /**
     * Output the URL of a CSS asset.
     *
     * When not in production mode the asset will be taken from the public path,
     * while on production will use the elixir helper function.
     *
     * @param  string     $asset_path asset file with relative path to the public folder
     * @param  string     $production_asset_path asset file to use on production environment, if not specified or null the $asset_path will be used
     * @return string the asset absolute URL
     *
     * @throws \InvalidArgumentException if asset is not found in elixir manifest
     * 
     * @deprecated use mix() and Laravel Mix
     */
    function css_asset($asset_path, $production_asset_path = null)
    {
        $excluded_env = ['testing', 'local', 'dev', 'development'];
        
        if (in_array(app()->environment(), $excluded_env)) {
            return url($asset_path).'?bust='.\Carbon\Carbon::now()->format('U');
        }
        
        return url(elixir(is_null($production_asset_path) ? $asset_path : $production_asset_path, ''));
    }
}

if (! function_exists('js_asset')) {
    /**
     * Output the URL of a JS asset.
     *
     * When not in production mode the asset will be taken from the public path,
     * while on production will use the elixir helper function.
     *
     * @param  string     $asset_path asset file with relative path to the public folder
     * @return string the asset absolute URL
     *
     * @throws \InvalidArgumentException if asset is not found in elixir manifest
     * 
     * @deprecated use mix() and Laravel Mix
     */
    function js_asset($asset_path)
    {
        $excluded_env = ['testing', 'local', 'dev', 'development'];
        
        if (in_array(app()->environment(), $excluded_env)) {
            return url($asset_path);
        }
        
        return url(elixir($asset_path));
    }
}


if (! function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return string
     *
     * @throws \Exception
     */
    function mix($path, $manifestDirectory = '')
    {
        static $manifests = [];

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        logs()->info($manifest);

        if (! isset($manifest[$path])) {
            $exception = new Exception("Unable to locate Mix file: {$path}.");

            if (! app('config')->get('app.debug')) {
                report($exception);

                return $path;
            } else {
                throw $exception;
            }
        }

        return url($manifestDirectory.$manifest[$path]);


    }
}

if (! function_exists('support_token')) {
    /**
     * Get the Support service authentication token
     *
     * @uses \KBox\Support\SupportService::token()
     *
     * @return string|null the support ticket integration token if configured, null if not configured
     */
    function support_token()
    {
        return \KBox\Support\SupportService::token();
    }
}

if (! function_exists('support_active')) {
    /**
     * Check if the specified support service is active
     *
     * @uses \KBox\Support\SupportService::active()
     *
     * @return boolean true if the support service is active, false otherwise
     */
    function support_active($service)
    {
        return \KBox\Support\SupportService::active($service);
    }
}

if (! function_exists('localized_date')) {
    /**
     * Convert a DateTime instance to a localizable date instance
     *
     * @uses Jenssegers\Date\Date
     *
     * @param  DateTime $dt a DateTime instance to be localized
     * @return Jenssegers\Date\Date the localizable date class instance
     */
    function localized_date(\DateTime $dt)
    {
        return Jenssegers\Date\Date::instance($dt);
    }
}

if (! function_exists('localized_date_human_diff')) {
    /**
     * Convert a DateTime instance to a localizable date instance
     *
     * @uses Jenssegers\Date\Date
     *
     * @param  DateTime $dt a DateTime instance to be localized
     * @return string the localized formatted date
     */
    function localized_date_human_diff(\DateTime $dt)
    {
        $dt = localized_date($dt);

        $diff = $dt->diffInDays();
        
        if ($diff < 2) {
            return $dt->diffForHumans();
        }

        return $dt->format(trans('units.date_format'));
    }
}

if (! function_exists('localized_date_full')) {
    /**
     * Convert a DateTime instance to a localizable date instance
     *
     * @uses Jenssegers\Date\Date
     *
     * @param  DateTime $dt a DateTime instance to be localized
     * @return string the localized formatted date
     */
    function localized_date_full(\DateTime $dt)
    {
        $dt = localized_date($dt);

        return $dt->format(trans('units.date_format_full'));
    }
}

if (! function_exists('localized_date_short')) {
    /**
     * Convert a DateTime instance to a localizable date instance
     *
     * @uses Jenssegers\Date\Date
     *
     * @param  DateTime $dt a DateTime instance to be localized
     * @return string the localized formatted date
     */
    function localized_date_short(\DateTime $dt)
    {
        $dt = localized_date($dt);

        return $dt->format(trans('units.date_format'));
    }
}

if (! function_exists('network_name')) {
    /**
     * Get the configured network name
     *
     * @return string the configured network name localized according to the current application locale
     */
    function network_name()
    {
        $opt_en = \Cache::rememberForever('network-name-en', function () {
            return \KBox\Option::option(\KBox\Option::PUBLIC_CORE_NETWORK_NAME_EN, '');
        });
        $opt_ru = \Cache::rememberForever('network-name-ru', function () {
            return \KBox\Option::option(\KBox\Option::PUBLIC_CORE_NETWORK_NAME_RU, '');
        });

        $locale = \App::getLocale();

        if ($locale === 'en' && ! empty($opt_en)) {
            return $opt_en;
        } elseif ($locale === 'ru' && ! empty($opt_en) && empty($opt_ru)) {
            return $opt_en;
        } elseif ($locale === 'ru' && ! empty($opt_ru)) {
            return $opt_ru;
        }

        return empty($opt_en) ? trans('networks.klink_network_name') : $opt_en;
    }
}

if (! function_exists('network_enabled')) {
    /**
     * Check if the network connection is enabled
     *
     * @return bool
     */
    function network_enabled()
    {
        return ! ! \KBox\Option::option(\KBox\Option::PUBLIC_CORE_ENABLED, false);
    }
}

if (! function_exists('analytics_token')) {
    /**
     * Get the analytics service tracking token
     *
     * @uses \KBox\Support\Analytics\Analytics::token()
     *
     * @return string|null the analytics site identifier/token
     */
    function analytics_token()
    {
        return \KBox\Support\Analytics\Analytics::token();
    }
}

if (! function_exists('flags')) {
    /**
     * Shortcut for accessing the KBox\Flags class
     *
     * @return KBox\Flags
     */
    function flags($key = null)
    {
        if (is_null($key)) {
            return new \KBox\Flags();
        }

        return \KBox\Flags::isEnabled($key);
    }
}

if (! function_exists('debugger') && env('APP_ENV') !== 'production') {
    /**
     * Create a PsySh shell (@link http://psysh.org/) in the context of the application.
     *
     * As seen on https://blog.tighten.co/supercharge-your-laravel-tinker-workflow
     *
     * @example debugger(get_defined_vars()) to have the current context variables in the shell environment
     * @return void
     */
    function debugger($mixed = null)
    {
        eval('\Psy\Shell::debug(isset($mixed) && !is_null($mixed) ? $mixed : get_defined_vars());');
    }
}

if (! function_exists('array_from')) {
    /**
     * Create an array from a given comma separated string
     *
     * @param string $value
     * @return array
     */
    function array_from($value)
    {
        if (is_null($value) || empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value)) {
            return [];
        }

        return explode(',', $value);
    }
}

if (! function_exists('elaborate')) {
    /**
     * Trigger.
     *
     * @param  mixed  $job
     * @return \KBox\DocumentsElaboration\DocumentElaborationManager
     */
    function elaborate($document = null)
    {
        if (is_null($document)) {
            return app(\KBox\DocumentsElaboration\DocumentElaborationManager::class);
        }

        return app(\KBox\DocumentsElaboration\DocumentElaborationManager::class)->queue($document);
    }
}

if (! function_exists('plugins')) {
    /**
     * Helper for accessing the PluginManager.
     *
     * If a plugin Id is specified, the helper return true if the plugin is enabled
     *
     * @param  string  $plugin The plugin identifier. Default null
     * @return \KBox\Plugins\PluginManager|bool An instance of the plugin manager, if $plugin is null, the status of the plugin otherwise
     */
    function plugins($plugin = null)
    {
        if (is_null($plugin)) {
            return app(PluginManager::class);
        }

        return app(PluginManager::class)->isEnabled($plugin);
    }
}

if (! function_exists('is_readonly')) {
    /**
     * Check if application is in readonly mode.
     *
     * @return bool
     */
    function is_readonly()
    {
        return app(ReadonlyMode::class)->isReadonlyActive();
    }
}

if (! function_exists('human_filesize')) {
    /**
     * Transform bytes in an understandable format
     *
     * @see \KBox\Documents\Services\DocumentsService::human_filesize()
     * @return string
     */
    function human_filesize($bytes, $decimals = 2)
    {
        return DocumentsService::human_filesize($bytes, $decimals);
    }
}
