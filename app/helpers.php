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

