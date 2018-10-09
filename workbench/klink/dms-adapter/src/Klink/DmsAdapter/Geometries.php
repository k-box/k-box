<?php

namespace Klink\DmsAdapter;

use KBox\File;
use proj4php\Wkt;
use KBox\FileProperties;
use Vicchi\GeoJson\Rewind;
use OneOffTech\GeoServer\Models\BoundingBox;
use OneOffTech\GeoServer\GeoType as BaseGeoType;
use Spinen\Geometry\GeometryFacade as Geometry;
use KSearchClient\Model\Data\GeographicGeometry;

/**
 * Helper for bounding boxes
 */
final class Geometries
{
    /**
     * Get the Geoserver bounding box as GeoJSON
     */
    public static function boundingBoxFromGeoserver($geoserverBbox)
    {
        if(is_null($geoserverBbox)){
            return null;
        }

        if($geoserverBbox instanceof BoundingBox){
            $geoserverBbox = $geoserverBbox->toArray();
            list($west, $south, $east, $north) = $geoserverBbox;
        }
        else {
            list('minX' => $west, 'minY' => $south, 'maxX' => $east, 'maxY' => $north) = $geoserverBbox;
        }


        $lowLeft = [$west, $south];
        $topLeft = [$west, $north];
        $topRight = [$east, $north];
        $lowRight = [$east, $south];

        return GeographicGeometry::polygon(static::ensurePolygonCoordinatesRespectGeoJson([
            $lowLeft,
            $lowRight,
            $topRight,
            $topLeft,
            $lowLeft,
        ]))->__toString();
    }
    
    /**
     * Takes a bbox and returns an equivalent GeoJSON
     * 
     * @param array $bbox extent in [minX, minY, maxX, maxY] order
     */
    public static function boundingBoxFromArray($bbox)
    {
        if(is_null($bbox) || !is_array($bbox)){
            return null;
        }

        list($west, $south, $east, $north) = $bbox;

        $lowLeft = [$west, $south];
        $topLeft = [$west, $north];
        $topRight = [$east, $north];
        $lowRight = [$east, $south];

        return GeographicGeometry::polygon(static::ensurePolygonCoordinatesRespectGeoJson([
            $lowLeft,
            $lowRight,
            $topRight,
            $topLeft,
            $lowLeft,
        ]))->__toString();
    }

    /**
     * Convert an array describing a bounding box to a leaftlet LatLng bounding box
     */
    public static function arrayAsLatLngBounds($array)
    {
        if(is_null($array)){
            return null;
        }
        list($west, $south, $east, $north) = $array;
        
        $bounds = sprintf('[[%2$s, %1$s], [%4$s, %3$s]]', $west, $south, $east, $north);

        return $bounds;
    }

    /**
     * Ensures that the coordinates of the polygon respect the right hand rule
     * Required the geo plugin to be enabled. Will silently fail, returning the passed coordinates, if the geo plugin is disabled
     * 
     * @uses Vicchi\GeoJson\Rewind
     * 
     */
    public static function ensurePolygonCoordinatesRespectGeoJson($coordinates)
    {
        if(class_exists(Rewind::class)){
            $source = [
                'type' => 'Polygon',
                'coordinates' => $coordinates
            ];
            $output = Rewind::rewind($source);
            return $output['coordinates'];
        }
        
        return $coordinates;
    }

    /**
     * Make sure to limit coordinates that are outside of the acceptable range
     * 
     * The current acceptable range is equal to BoundingBoxFilter::worldBounds()
     * 
     * @param array $coordinates an array describing the bounding box coordinates
     * @return array
     */
    public static function ensureCoordinatesWithinAcceptableRange(array $coordinates)
    {
        list($west, $south, $east, $north) = $coordinates;

        if($west <= -180){
            $west = -179.99;
        }

        if($east >= 180){
            $east = 179.99;
        }

        if($south <= -90){
            $south = -89.99;
        }

        if($north >= 90){
            $north = 89.99;
        }

        return [$west, $south, $east, $north];

    }

}
