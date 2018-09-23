<?php

namespace KBox\Geo;

use KBox\File;
use proj4php\Wkt;
use KBox\FileProperties;
use OneOffTech\GeoServer\GeoType as BaseGeoType;

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
    

    public static function fromGdalOutput(string $output)
    {
        $decoded = collect(json_decode($output, true) ?? [])->dump();

        if($decoded->isEmpty()){
            throw new Exception("Failed to decode GDAL output");
        }

        $size = $decoded->get('size', []);
        $coordinateSystem = $decoded->get('coordinateSystem', null);

        if($coordinateSystem){
            $wkt = preg_replace('/\s+/', '', optional($coordinateSystem)['wkt'] ?? '');
            $parsedWkt = Wkt::Parse($wkt);
            $projection = $parsedWkt ? $parsedWkt->srsCode : '';
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

}
