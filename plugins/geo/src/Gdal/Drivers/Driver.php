<?php

namespace KBox\Geo\Gdal\Drivers;

use Exception;
use SplFileInfo;
use KBox\Geo\GeoFile;
use KBox\Geo\GeoType;
use KBox\Geo\Gdal\Gdal;
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
     * Convert a vector geographic file into a different format
     * 
     * @param string $path
     * @param string $destination
     * @param string $format
     * @param string $crs
     * @return SplFileInfo the destination file instance
     */
    public function convert($path, $destination, $format, $crs = null)
    {
        $geoFile = GeoFile::from($path);

        if($geoFile->type === GeoType::RASTER){
            throw new Exception("Raster file conversion not supported. Use convertRaster().");
        }

        $alternateLayerName = md5(basename($path));

        $options = ["-f \"$format\""];

        if($format === Gdal::FORMAT_SHAPEFILE){
            $options[] = "-append";
            $options[] = "-nln {$alternateLayerName}";
            $options[] = "-nlt GEOMETRY";
            $options[] = "-skipfailure";
        }

        if(!is_null($crs)){
            $options[] = "-t_srs \"$crs\"";
        }


        \Log::info('conversion to shapefile', compact('options', 'path', 'destination'));

        $result = $this->execute(static::VECTOR_CONVERT_EXECUTABLE, [$destination, $path], $options);

        return new SplFileInfo($destination);
    }

    /**
     * Convert a raster geographic file into a different format
     * 
     * @param string $path
     * @param string $destination
     * @param string $format
     * @param array $options Parameter to pass to the gdal_translate binary
     * @return SplFileInfo the destination file instance
     */
    public function convertRaster($path, $destination, $format, $options = [])
    {
        $geoFile = GeoFile::from($path);

        if($geoFile->type === GeoType::VECTOR){
            throw new Exception("Vector file conversion not supported. Use convert().");
        }

        $alternateLayerName = md5(basename($path));

        $options = array_merge(["-of \"$format\""], $options);

        // if($format === Gdal::FORMAT_SHAPEFILE){
        //     $options[] = "-append";
        //     $options[] = "-nln {$alternateLayerName}";
        //     $options[] = "-nlt GEOMETRY";
        //     $options[] = "-skipfailure";
        // }

        // if(!is_null($crs)){
        //     $options[] = "-t_srs \"$crs\"";
        // }


        \Log::info('raster file conversion', compact('options', 'path', 'destination'));

        $result = $this->execute(static::RASTER_CONVERT_EXECUTABLE, [$path, $destination], $options);

        return new SplFileInfo($destination);
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
