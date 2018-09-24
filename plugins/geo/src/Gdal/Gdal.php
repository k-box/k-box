<?php

namespace KBox\Geo\Gdal;

use KBox\Geo\GeoProperties;
use KBox\Geo\Gdal\Drivers\WindowsDriver;

/**
 * GDAL library wrapper
 * 
 * Deliver, in a comfortable way, the GDAL utilities that we currently need
 */
final class Gdal
{

    const FORMAT_GEOJSON = "GeoJSON";

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
     * @param string $format the target format
     * @return mixed
     */
    public function convert($path, $format)
    {

        // shapefile to GeoJSON
        // ogr2ogr \
        // -f 'GeoJSON' \
        // -t_srs 'EPSG:4326' \
        // $NEWDIR$FILENEW $FILE

        // GeoJSON to shapefile
        // ogr2ogr \
        // -f "ESRI Shapefile" \
        // $NEWDIR$FILENEW $FILE
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
        return $this->driver ?? ($this->driver = new WindowsDriver());
    }

}
