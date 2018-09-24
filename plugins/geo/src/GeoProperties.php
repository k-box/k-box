<?php

namespace KBox\Geo;

use KBox\File;
use proj4php\Wkt;
use KBox\FileProperties;
use OneOffTech\GeoServer\GeoType as BaseGeoType;
use Spinen\Geometry\GeometryFacade as Geometry;

/**
 * File properties that targets georeferenced files
 * 
 * This properties will be stored as File::$properties
 * 
 * @property $crs
 * @property string $crs.label The Coordinate Reference System label, e.g. EPSG:4326
 * @property string $crs.wkt The Coordinate Reference System Well Known Text (WKT) representation
 * @property array $layers The name of the layers inside the file
 * @property string $boundings.latlon The bounding box expressed with WGS84 coordinates
 * @property string $boundings.wkt The bounding box, expressed in Well Known Text format
 * @property string $boundings.original The original bounding box, as extracted from the file
 * @property string $type The type of the file, @see \KBox\Geo\GeoType
 * @property string $dimension The file dimension in pixels. Available only for GeoType::RASTER files
 * 
 */
final class GeoProperties extends FileProperties
{
    protected $exclude = ['layers', 'boundingBox', 'boundings.geoserver'];
    

    private static function parseCrsWkt($wkt)
    {
        $clean_wkt = preg_replace('/\s+/', '', $wkt ?? '');
        $parsedWkt = Wkt::Parse($clean_wkt);
        return $parsedWkt ? $parsedWkt->srsCode : '';
    }

    private static function parseExtentToWkt($extent)
    {

        $re = '/\((-?[\d\.]*),\s?(-?[\d\.]*)\)/';

        preg_match_all($re, $extent, $matches, PREG_SET_ORDER, 0);

        if(count($matches) !== 2){
            return '';
        }

        return "POLYGON(({$matches[0][1]} {$matches[0][2]},{$matches[1][1]} {$matches[0][2]},{$matches[1][1]} {$matches[1][2]},{$matches[0][1]} {$matches[0][2]}))";
    }

    public static function fromGdalOutput(string $output)
    {
        $decoded = collect(json_decode($output, true) ?? []);

        if($decoded->isEmpty()){
            throw new Exception("Failed to decode GDAL output");
        }

        $size = $decoded->get('size', []);
        $coordinateSystem = $decoded->get('coordinateSystem', null);

        if($coordinateSystem){
            $projection = static::parseCrsWkt(optional($coordinateSystem)['wkt'] ?? '');
        }


        $attributes = collect([
            'type' => GeoType::RASTER,
            'dimension' => [
                'width' => $size[0] ?? null,
                'height' => $size[1] ?? null,
            ],
            'crs' => [
                'label' => $projection ?? '',
                'wkt' => optional($coordinateSystem)['wkt'],
            ],
            'layers' => [],
            'boundings' => [
                'geojson' =>  json_encode($decoded->get('wgs84Extent', null)),
                'wkt' => null,
                'original' => json_encode($decoded->get('wgs84Extent', null)),
            ],
        ]);

        return static::fromCollection($attributes);
    }


    public static function fromOgrOutput(string $output)
    {
        $lines = collect(preg_split('/$\R?^/m', $output));
        
        $parsed = [];
        $key = null;
        
        foreach ($lines as $line) {
            if(str_contains($line, ':')){
                $key = str_slug(str_before($line, ':'));
                $value = str_after($line, ':');
                $parsed[$key] = trim($value);
            }
            else if($key != null && isset($parsed[$key])) {
                $parsed[$key] = $parsed[$key] . trim($line);
            }
            
        }

        $projection = static::parseCrsWkt($parsed['layer-srs-wkt'] ?? '');

        $wktExtent = static::parseExtentToWkt($parsed['extent']);

        $attributes = collect([
            'type' => GeoType::VECTOR,
            'crs' => [
                'label' => $projection ?? '',
                'wkt' => $parsed['layer-srs-wkt'],
            ],
            'layers' => array_wrap($parsed['layer-name']),
            'boundings' => [
                'geojson' => app('geometry')->parseWkt($wktExtent)->toGeoJson(),
                'wkt' => $wktExtent,
                'original' => $parsed['extent'],
            ],
        ]);

        return static::fromCollection($attributes);
    }

}
