<?php

namespace KBox\Geo\Gdal\Drivers;

use KBox\Geo\GeoFile;
use KBox\Geo\GeoType;
use KBox\Geo\GeoProperties;
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
     * Execute a Gdal binary
     */
    protected abstract function execute($command, $inputs = [], $options = []);

    /**
     * Return the library version
     * 
     * @return string
     */
    public function version()
    {
        return trim($this->execute(static::RASTER_INFO_EXECUTABLE, [], ['--version']), " \t\n\r\0\x0B\x0C");
    }

    /**
     * Get file information
     * 
     * @return GeoProperties
     */
    public function info($path)
    {
        $geoFile = GeoFile::from($path);

        $result = $this->execute($geoFile->type === GeoType::RASTER ? static::RASTER_INFO_EXECUTABLE : static::VECTOR_INFO_EXECUTABLE, [$path], $geoFile->type === GeoType::RASTER ? ['-json', '-proj4'] : ['-al', '-so', '-nomd']);
        
        if($geoFile->type === GeoType::RASTER){
            return GeoProperties::fromGdalOutput($result);
        }

        return GeoProperties::fromOgrOutput($result);
    }


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
