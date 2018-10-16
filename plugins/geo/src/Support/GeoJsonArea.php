<?php

namespace KBox\Geo\Support;

use Vicchi\GeoJson\Rewind;

/**
 * GeoJson area.
 * 
 * The code below is part of https://github.com/vicchi/geojson-rewind
 * 
 * Copyright (c) 2018, Gary Gale
 */
final class GeoJsonArea
{
    const RADIUS = 6378137;
    const FLATTENING_DENOM = 298.257223563;
    const FLATTENING = 1 / self::FLATTENING_DENOM;
    const POLAR_RADIUS = self::RADIUS * (1 - self::FLATTENING);

    static function geometry($geojson) {
        $area = 0;
        switch ($geojson['type']) {
            case 'Polygon':
                return self::polygonArea($geojson['coordinates']);

            case 'MultiPolygon':
                foreach ($geojson['coordinates'] as $coords) {
                    $area += self::polygonArea($coords);
                }
                return $area;

            case 'Point':
            case 'MultiPoint':
            case 'LineString':
            case 'MultiLineString':
                return 0;

            case 'GeometryCollection':
                foreach ($geojson['geometries'] as $geometry) {
                    $area += self::geometry($geometry);
                }
                return $area;

            default:
                throw new \Exception('Unknown GeoJSON type "' . $geojson['type'] . '"');
        }
    }

    static function polygonArea($coords) {
        $area = 0;
        if (!empty($coords) && (count($coords) > 0)) {
            $area += abs(self::ringArea($coords[0]));
            array_shift($coords);
            foreach($coords as $coord) {
                $area -= abs(self::ringArea($coord));
            }
        }

        return $area;
    }

    /**
    * Calculate the approximate area of the polygon were it projected onto
    *     the earth.  Note that this area will be positive if ring is oriented
    *     clockwise, otherwise it will be negative.
    *
    * Reference:
    * Robert. G. Chamberlain and William H. Duquette, "Some Algorithms for
    *     Polygons on a Sphere", JPL Publication 07-03, Jet Propulsion
    *     Laboratory, Pasadena, CA, June 2007 http://trs-new.jpl.nasa.gov/dspace/handle/2014/40409
    *
    * Returns:
    * {float} The approximate signed geodesic area of the polygon in square
    *     meters.
    */

    static function ringArea($coords) {
        $area = 0;
        $len = count($coords);
        if ($len > 2) {
            for ($i = 0; $i < $len; $i++) {
                if ($i === ($len - 2)) {
                    $lowerIndex = $len - 2;
                    $middleIndex = $len - 1;
                    $upperIndex = 0;
                }

                else if ($i === ($len - 1)) {
                    $lowerIndex = $len - 1;
                    $middleIndex = 0;
                    $upperIndex = 1;
                }

                else {
                    $lowerIndex = $i;
                    $middleIndex = $i + 1;
                    $upperIndex = $i + 2;
                }

                $p1 = $coords[$lowerIndex];
                $p2 = $coords[$middleIndex];
                $p3 = $coords[$upperIndex];

                $area += (self::rad($p3[0]) - self::rad($p1[0])) * sin(self::rad($p2[1]));
            }

            $area = $area * self::RADIUS * self::RADIUS / 2;
        }

        return $area;
    }

    static function rad($coord) {
        return $coord * M_PI / 180;
    }
}
