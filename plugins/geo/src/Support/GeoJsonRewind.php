<?php

namespace KBox\Geo\Support;

use Vicchi\GeoJson\Rewind;

/**
 * GeoJson rewind.
 * 
 * Make sure that polygons respect the GeoJson right-hand rule, 
 * as indicated in [RFC7946](https://tools.ietf.org/html/rfc7946#section-3.1.6)
 * 
 * @uses \Vicchi\GeoJson\Rewind if available
 * 
 */
final class GeoJsonRewind
{
    public static function rewind(array $geojson, $enforce_rfc7946=true)
    {
        if(class_exists(Rewind::class)){
            
            return Rewind::rewind($geojson, $enforce_rfc7946);
            
        }

        return $geojson;
    }

    public static function rewindCoordinates(array $coordinates)
    {
        $source = [
            'type' => 'Polygon',
            'coordinates' => $coordinates
        ];
        $output = self::rewind($source);
        return $output['coordinates'];
    }

}
