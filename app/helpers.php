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
        
        if( in_array( app()->environment(), $excluded_env) ){
            return url( $asset_path );
        } 
        
        return url(elixir( is_null($production_asset_path) ? $asset_path : $production_asset_path ));
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
        
        if( in_array( app()->environment(), $excluded_env) ){
            return url( $asset_path );
        } 
        
        return url(elixir( $asset_path ));
    }
}


if (! function_exists('support_token')) {
    /**
     * Get the Support service authentication token
     *
     * @uses \KlinkDMS\Option::support_token()
     *
     * @return string|boolean the support ticket integration token if configured, false if not configured
     */
    function support_token()
    {
        
        return \KlinkDMS\Option::support_token(); 
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
     * @return Jenssegers\Date\Date the localizable date class instance
     */
    function localized_date_human_diff(\DateTime $dt)
    {

        $dt = localized_date($dt);

        $diff = $dt->diffInDays();
        
        if($diff < 2){
            return $dt->diffForHumans();
        }

        return $dt->format( trans('units.date_format') ); 
    }
}

if (! function_exists('localized_date_full')) {
    /**
     * Convert a DateTime instance to a localizable date instance
     *
     * @uses Jenssegers\Date\Date
     *
     * @param  DateTime $dt a DateTime instance to be localized
     * @return Jenssegers\Date\Date the localizable date class instance
     */
    function localized_date_full(\DateTime $dt)
    {

        $dt = localized_date($dt);

        return $dt->format( trans('units.date_format_full') ); 
    }
}

if (! function_exists('localized_date_short')) {
    /**
     * Convert a DateTime instance to a localizable date instance
     *
     * @uses Jenssegers\Date\Date
     *
     * @param  DateTime $dt a DateTime instance to be localized
     * @return Jenssegers\Date\Date the localizable date class instance
     */
    function localized_date_short(\DateTime $dt)
    {

        $dt = localized_date($dt);

        return $dt->format( trans('units.date_format') ); 

    }

}


if (! function_exists('network_name')) {
    /**
     * Get the configured network name
     *
     *
     * @return string the configured network name localized according to the current application locale
     */
    function network_name()
    {

        $opt_en = \Cache::rememberForever('network-name-en', function() {
            return \KlinkDMS\Option::option(\KlinkDMS\Option::PUBLIC_CORE_NETWORK_NAME_EN, '');
        });
        $opt_ru = \Cache::rememberForever('network-name-ru', function() {
            return \KlinkDMS\Option::option(\KlinkDMS\Option::PUBLIC_CORE_NETWORK_NAME_RU, '');
        });

        $locale = \App::getLocale();
        
        if($locale === 'en' && !empty($opt_en)){
            return $opt_en;
        }
        else if($locale === 'ru' && !empty($opt_en) && empty($opt_ru)){
            return $opt_en;
        }
        else if($locale === 'ru' && !empty($opt_ru)){
            return $opt_ru;
        }

        return trans('networks.klink_network_name'); 
    }
}

if (! function_exists('analytics_token')) {
    /**
     * Get the analytics service tracking token
     *
     * @uses \KlinkDMS\Option::analytics_token()
     *
     * @return string|boolean the analytics site identifier/token
     */
    function analytics_token()
    {
        
        return \KlinkDMS\Option::analytics_token(); 
    }
}

