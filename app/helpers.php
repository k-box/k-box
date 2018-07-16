<?php


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
     */
    function css_asset($asset_path, $production_asset_path = null)
    {
        $excluded_env = ['testing', 'local', 'dev', 'development'];
        
        if (in_array(app()->environment(), $excluded_env)) {
            return url($asset_path).'?bust='.\Carbon\Carbon::now()->format('U');
        }
        
        return url(elixir(is_null($production_asset_path) ? $asset_path : $production_asset_path));
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

if (! function_exists('support_token')) {
    /**
     * Get the Support service authentication token
     *
     * @uses \KBox\Option::support_token()
     *
     * @return string|boolean the support ticket integration token if configured, false if not configured
     */
    function support_token()
    {
        return \KBox\Option::support_token();
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
     * @uses \KBox\Option::analytics_token()
     *
     * @return string|boolean the analytics site identifier/token
     */
    function analytics_token()
    {
        return \KBox\Option::analytics_token();
    }
}

if (! function_exists('flags')) {
    /**
     * Shortcut for accessing the KBox\Flags class
     *
     * @return KBox\Flags
     */
    function flags()
    {
        return new \KBox\Flags();
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
        if(is_null($value) || empty($value)){
            return [];
        }

        if(is_array($value)){
            return $value;
        }

        if(!is_string($value)){
            return [];
        }

        return explode(',', $value);
    }
}
