<?php

namespace KBox\Geo\Support;

use KBox\Geo\GeoType;
use KBox\Geo\GeoFormat;
use Illuminate\Support\Facades\Storage;
use OneOffTech\GeoServer\Support\ZipReader;
use OneOffTech\GeoServer\Support\TextReader;
use JsonSchema\Validator as JsonSchemaValidator;
use OneOffTech\GeoServer\Support\TypeResolver as GeoServerTypeResolver;

/**
 * Type resolver.
 * 
 * Identify geographic file format, type and mime type
 * 
 * @uses \OneOffTech\GeoServer\Support\TypeResolver
 * 
 */
final class TypeResolver
{
    protected static $mimeTypes = [
        GeoFormat::GEOJSON => 'application/geo+json', // geojson
        GeoFormat::KML => 'application/vnd.google-earth.kml+xml', // Keyhole Markup Language
        GeoFormat::KMZ => 'application/vnd.google-earth.kmz', // KML in ZIP container
        GeoFormat::GPX => 'application/gpx+xml', // GPS eXchange Format
        GeoFormat::GEOPACKAGE => 'application/geopackage+sqlite3', // GPS eXchange Format
    ];
    
    protected static $mimeTypeToFormat = [
        'application/geo+json' => GeoFormat::GEOJSON ,
        'application/vnd.google-earth.kml+xml' => GeoFormat::KML ,
        'application/vnd.google-earth.kmz' => GeoFormat::KMZ ,
        'application/gpx+xml' => GeoFormat::GPX,
        'application/geopackage+sqlite3' => GeoFormat::GEOPACKAGE,
    ];

    protected static $typesMap = [
        GeoFormat::GEOJSON => GeoType::VECTOR,
        GeoFormat::KML => GeoType::VECTOR,
        GeoFormat::KMZ => GeoType::VECTOR,
        GeoFormat::GPX => GeoType::VECTOR,
        GeoFormat::GEOPACKAGE => GeoType::VECTOR,
        GeoFormat::GEOTIFF => GeoType::RASTER,

        GeoType::VECTOR => [
            GeoFormat::GEOJSON,
            GeoFormat::KML,
            GeoFormat::KMZ,
            GeoFormat::GPX,
            GeoFormat::GEOPACKAGE,
        ],
    ];

    /**
     * The file extension, given the file format, as accepted by GeoServer
     */
    protected static $normalizedFormatFileExtensions = [
        GeoFormat::SHAPEFILE => 'shp',
        GeoFormat::SHAPEFILE_ZIP => 'zip',
        GeoFormat::GEOJSON => 'json',
        GeoFormat::KML => 'kml',
        GeoFormat::KMZ => 'kmz',
        GeoFormat::GPX => 'gpx',
        GeoFormat::GEOTIFF => 'geotiff',
        GeoFormat::GEOPACKAGE => 'gpkg',
    ];

    /**
     * The file mime type, given the file format, as accepted by GeoServer
     */
    protected static $normalizedMimeTypeFileFormat = [];
    

    /**
     * Identify the type, format and mime type of a given file
     * 
     * @param string $path the file path on disk
     * @return array with [format, type, mimeType]
     */
    public static function identify($path)
    {
        $absolute_path = @is_file($path) ? $path : Storage::path($path);
        \Log::warning('TypeResolver::identify', [$path, $absolute_path]);
        list($format, $defaultType, $mimeType) = GeoServerTypeResolver::identify($absolute_path);

        if ($mimeType === 'application/json' || $mimeType === 'text/plain') {

            // check if is a GeoJSON, but enclosed in a plain json/text file
            $validator = new JsonSchemaValidator;
            $content = json_decode(file_get_contents($absolute_path));
            $schema = json_decode(file_get_contents(__DIR__ . '/../../schemas/geojson.json'));

            $result = $validator->validate($content, $schema);

            if ($validator->isValid()) {
                $format = GeoFormat::GEOJSON;
                $mimeType = self::$mimeTypes[GeoFormat::GEOJSON];
            }
        } elseif ($mimeType === 'application/zip') {

            // could be a KMZ or a compressed shapefile

            // Check if KMZ, By definition in https://developers.google.com/kml/documentation/kmzarchives
            // a KMZ contains only a main KML file that ends with .kml extension
            $containsKml = ZipReader::containsFile($absolute_path, '.kml');

            if ($containsKml) {
                $format = GeoFormat::KMZ;
                $mimeType = self::$mimeTypes[GeoFormat::KMZ];
            }
            
        } elseif ($mimeType === 'application/xml') {

            // could be KML or GPX

            // check if KML tag is present
            $data = join('', TextReader::readLines($absolute_path, 2));

            if (strpos($data, '<kml') !== false) {
                $format = GeoFormat::KML;
                $mimeType = self::$mimeTypes[GeoFormat::KML];
            } elseif (strpos($data, '<gpx') !== false) {
                // http://www.topografix.com/gpx/1/1/gpx.xsd
                $format = GeoFormat::GPX;
                $mimeType = self::$mimeTypes[GeoFormat::GPX];
            }
        }
        
        $type = self::convertFormatToType($format);
    
        return [
            $format,
            $type,
            $mimeType
        ];
    }

    public static function supportedMimeTypes()
    {
        return array_merge(GeoServerTypeResolver::supportedMimeTypes(), array_value(static::$mimeTypes));
    }
    
    public static function supportedFormats()
    {
        return array_merge(GeoServerTypeResolver::supportedFormats(), array_keys(static::$mimeTypes));
    }

    public static function convertFormatToType($format)
    {
        return GeoServerTypeResolver::convertFormatToType($format) ?? 
            (!is_null($format) && isset(static::$typesMap[$format]) ? static::$typesMap[$format] : null);
    }

    public static function convertFormatToMimeType($format)
    {
        return GeoServerTypeResolver::convertFormatToMimeType($format) ?? 
            (!is_null($format) && isset(static::$mimeTypes[$format]) ? static::$mimeTypes[$format] : null);
    }

    /**
     * Get the GeoServer accepted file extension for the specific file format
     * 
     * @return string|null The normalized extension or null in case no conversion is required
     */
    public static function normalizedExtensionFromFormat($format)
    {
        return GeoServerTypeResolver::normalizedExtensionFromFormat($format) ?? 
            (!is_null($format) && isset(static::$normalizedFormatFileExtensions[$format]) ? static::$normalizedFormatFileExtensions[$format] : null);
    }

    /**
     * Get the GeoServer accepted file mime type for the specific file format
     * 
     * @return string|null The normalized mime type or null in case no conversion is required
     */
    public static function normalizedMimeTypeFromFormat($format)
    {
        return GeoServerTypeResolver::normalizedMimeTypeFromFormat($format) ??
            (!is_null($format) && isset(static::$normalizedMimeTypeFileFormat[$format]) ? static::$normalizedMimeTypeFileFormat[$format] : null);
    }
}
