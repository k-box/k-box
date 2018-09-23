<?php

namespace KBox\Geo\Gdal\Drivers;

use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * GDAL driver
 * 
 * Abstract GDAL calls for the specific platform and installation
 */
abstract class Driver
{
    /**
     * The binary folder that contains the GDAL installation
     * 
     * @var string
     */
    const BIN_FOLDER = 'plugins/geo/bin';
    

    /**
     * Return the library version
     * 
     * @return string
     */
    public abstract function version();

    /**
     * Get file information
     * 
     * @return GeoProperties
     */
    public abstract function info($path);


    /**
     * Check if GDal is available on the system
     * 
     * @return bool
     */
    public function isAvailable()
    {
        try{

            return $this->version() ? true : false;

        }catch(ProcessFailedException $ex){
            return false;
        }
    }
}
