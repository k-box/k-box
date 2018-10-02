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
        }

        list('minX' => $west, 'minY' => $south, 'maxX' => $east, 'maxY' => $north) = $geoserverBbox;

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
            $topLeft,
            $topRight,
            $lowRight,
            $lowLeft,
        ]))->__toString();
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

}
