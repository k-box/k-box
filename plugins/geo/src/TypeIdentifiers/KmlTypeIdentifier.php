<?php

namespace KBox\Geo\TypeIdentifiers;

use KBox\Geo\GeoType;
use KBox\Geo\GeoFormat;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentifier;
use KBox\Geo\Support\TypeResolver;
use KBox\Documents\TypeIdentification;
use KBox\Documents\Exceptions\UnsupportedFileException;

class KmlTypeIdentifier extends TypeIdentifier
{
    public $accept = ["application/zip", "application/vnd.google-earth.kml+xml", "application/vnd.google-earth.kmz"];

    public $priority = 1;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        list($format, $type, $mimeType) = TypeResolver::identify($path);

        if ($format === GeoFormat::KML || $format === GeoFormat::KMZ) {
            return new TypeIdentification($mimeType, DocumentType::GEODATA);
        }

        throw UnsupportedFileException::path($path);
    }
}