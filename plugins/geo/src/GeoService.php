<?php

namespace KBox\Geo;

use Exception;
use KBox\Plugins\PluginManager;
use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Auth\Authentication;
use OneOffTech\GeoServer\Exception\GeoServerClientException;

final class GeoService
{
    /**
     * The plugin identifier
     */
    const PLUGIN_ID = 'k-box-kbox-plugin-geo';

    private $manager = null;

    public function __construct(PluginManager $manager)
    {
        $this->manager = $manager;
    }



    /**
     * Get / set the plugin configuration.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed|array
     */
    public function config($key = null, $default = null)
    {

        if (is_null($key)) {
            $config = $this->manager->config(self::PLUGIN_ID);

            if(isset($config['geoserver_password'])){
                $config['geoserver_password'] = decrypt($config['geoserver_password']);
            }

            return $config;
        }

        if (is_array($key)) {

            if(isset($key['geoserver_password'])){
                $key['geoserver_password'] = encrypt($key['geoserver_password']);
            }

            $this->manager->config(self::PLUGIN_ID, $key);
        }
        
        
        $value = $this->manager->config(self::PLUGIN_ID, $key, $default);

        if($key === 'geoserver_password' && !is_null($value) && $value !== $default){
            return decrypt($value);
        }

        return $value;

    }


    /**
     * If the plugin services can be used.
     * 
     * Checks if the configuration is correct and can be used
     * 
     * @return bool
     */
    public function isEnabled()
    {
        $conf = $this->config();
        return is_array($conf) && count($conf) === 4;
    }


    public function connection()
    {
        $conf = $this->config();
        $authentication = new Authentication($conf['geoserver_username'], $conf['geoserver_password']);
        $geoserver = GeoServer::build($conf['geoserver_url'], $conf['geoserver_workspace'], $authentication);

        return $geoserver;
    }
    


    /**
     * Test if the given configuration is valid
     * 
     * The test check if the GeoServer version number can be retrieved by instantiating a new GeoServer client
     * 
     * @param array{geoserver_username:string,geoserver_password:string,geoserver_url:string,geoserver_workspace:string} $parameters
     * @throws Exception if a connection cannot be established
     */
    public static function testConnection(array $parameters)
    {
        try{

            $authentication = new Authentication($parameters['geoserver_username'], $parameters['geoserver_password']);

            $geoserver = GeoServer::build($parameters['geoserver_url'], $parameters['geoserver_workspace'], $authentication);

            $geoserver->version();

            return true;

        } catch(GeoServerClientException $ex){
            throw new Exception($ex->getMessage(), 1234567825, $ex);
        }
    }
}
