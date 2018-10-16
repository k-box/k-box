<?php

namespace KBox\Geo;

use Log;
use Exception;
use KBox\File;
use KBox\Geo\Gdal\Gdal;
use KBox\Plugins\PluginManager;
use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Auth\Authentication;
use KBox\Geo\Exceptions\FileConversionException;
use KBox\Geo\Exceptions\GeoServerUnsupportedFileException;
use OneOffTech\GeoServer\Exception\GeoServerClientException;

final class GeoService
{
    /**
     * The plugin identifier
     */
    const PLUGIN_ID = 'k-box-kbox-plugin-geo';

    /**
     * Formats supported natively by Geoserver
     */
    const GEOSERVER_SUPPORTED_FILES = [
        GeoFormat::SHAPEFILE_ZIP,
        GeoFormat::SHAPEFILE,
        GeoFormat::GEOTIFF,
        GeoFormat::GEOPACKAGE,
    ];

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
     * Get the default configuration of the plugin, as it was not changed
     * 
     * @param  string  $key The configuration key to retrieve. Default null, all configuration options are retrieved
     * @return mixed
     */
    public function defaultConfig($key = null)
    {
        return $this->manager->defaultConfig(self::PLUGIN_ID, $key);
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
        $configuration = $this->config();

        $conf = collect($configuration)->only(['geoserver_username','geoserver_password','geoserver_url','geoserver_workspace'])->filter();

        return $conf->count() === 4;
    }


    /**
     * Get the underlying geoserver connection
     * 
     * @return \OneOffTech\GeoServer\GeoServer
     */
    public function connection()
    {
        $conf = $this->config();
        $authentication = new Authentication($conf['geoserver_username'], $conf['geoserver_password']);
        $geoserver = GeoServer::build($conf['geoserver_url'], $conf['geoserver_workspace'], $authentication);

        return $geoserver;
    }


    /**
     * Check if the given file is supported
     * 
     * @param File $file
     * @return bool
     */
    public function isSupported(File $file)
    {
        return GeoFile::isSupported($file->absolute_path);
    }


    /**
     * Upload a file to the geoserver
     */
    public function upload($file)
    {
        $data = $this->asGeoFile($file);

        Log::info("Uploading to geoserver", $data->toArray());

        // If the file is not natively supported and is a vector file, we attempt to convert it
        if(!in_array($data->format,self::GEOSERVER_SUPPORTED_FILES)){

            if($data->type !== GeoType::VECTOR){
                throw new GeoServerUnsupportedFileException("File with format [$data->format] is not natively supported by Geoserver. Use " . implode(',', self::GEOSERVER_SUPPORTED_FILES));
            }

            Log::info("Performing on the flight conversion to shapefile", $data->toArray());

            $data = $data->convert(Gdal::FORMAT_SHAPEFILE)->name($data->name);

        }
        
        return $this->connection()->upload($data);
    }

    /**
     * Wrap a File instance into a GeoFile
     * 
     * @return GeoFile
     */
    public function asGeoFile($file)
    {
        return $file instanceof GeoFile ? $file : GeoFile::fromFile($file);
    }
    
    public function exist(File $file)
    {
        $data = $this->asGeoFile($file);
            
        return $this->connection()->exist($data);
    }

    /**
     * Generate the thumbnail of a file
     * 
     */
    public function thumbnail(File $file)
    {
        $data = $this->asGeoFile($file);

        Log::info("Generating thumbnail for $file->uuid using geoserver...");
            
        return $this->connection()->thumbnail($data);
    }
    

    /**
     * Get the Geoserver WMS service base url
     */
    public function wmsBaseUrl()
    {
        $conf = $this->config();

        return sprintf("%s/%s/wms", $conf['geoserver_url'], $conf['geoserver_workspace']);
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
