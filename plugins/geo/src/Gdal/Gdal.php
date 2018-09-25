<?php

namespace KBox\Geo\Gdal;

use KBox\Geo\GeoProperties;
use KBox\Geo\Gdal\Drivers\WindowsDriver;
use KBox\Geo\Gdal\Drivers\LinuxDriver;

/**
 * GDAL library wrapper
 * 
 * Deliver, in a comfortable way, the GDAL utilities that we currently need
 */
final class Gdal
{

    const FORMAT_GEOJSON = "GeoJSON";
    
    const FORMAT_PDF = "PDF";

    const FORMAT_SHAPEFILE = "ESRI Shapefile";


    /**
     * WGS 84 projection
     */
    const CRS_EPSG4326 = "EPSG:4326";

    /**
     * Web/Spherical Mercator Projection
     */
    const CRS_EPSG3857 = "EPSG:3857";


    private $driver = null;

    /**
     * Create a Gdal instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Check if GDal is installed
     * 
     * @return bool
     */
    public function isInstalled()
    {
        return $this->driver()->isAvailable();
    }

    /**
     * Return the GDAL version used
     * 
     * @return string
     */
    public function version()
    {
        return $this->driver()->version();
    }

    /**
     * Get metadata information of a file
     * 
     * @param string $path the file absolute path
     * @return GeoProperties
     */
    public function info($path) : GeoProperties
    {
        return $this->driver()->info($path);
    }

    /**
     * Convert a file into a different format
     * 
     * @param string $path the file absolute path
     * @param string $format the target file format. See FORMAT constants in this class for available formats
     * @param string $crs the target coordinate reference system. Default null, no coordinate conversion
     * @return SplFileInfo a temporary file that contains the conversion result. Unlink the file when not anymore necessary.
     */
    public function convert($path, $format, $crs = null)
    {
        // creating a temporary filename where the content 
        // of the converted file is stored
        $tmpfilename = tempnam($temporaryFolder ?? sys_get_temp_dir(), basename($path));

        return $this->driver()->convert($path, $tmpfilename, $format, $crs);
    }

    /**
     * Re-Project a file to a different coordinate system
     * 
     * @param string $path the file absolute path
     * @param string $targetCoordinate the target coordinate reference system
     * @param string $sourceCoordinate the source coordinate reference system. Default null, will be inferred from the file
     * @return mixed
     */
    public function transform($path, $targetCoordinate, $sourceCoordinate = null)
    {

    }

    /**
     * Get the Gdal driver to use for the current system
     */
    private function driver()
    {   
        $driverName = '\\KBox\\Geo\\Gdal\\Drivers\\' . (strtolower(PHP_OS) === 'winnt' ? 'Windows' : 'Linux') . 'Driver';
        return $this->driver ?? ($this->driver = new $driverName());
    }

}
