<?php

namespace KBox\Geo\TypeIdentifiers;

use KBox\Geo\GeoType;
use KBox\Geo\GeoFormat;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentifier;
use KBox\Geo\Support\TypeResolver;
use KBox\Documents\TypeIdentification;
use KBox\Documents\Exceptions\UnsupportedFileException;

class GeoJsonTypeIdentifier extends TypeIdentifier
{
    public $accept = ["text/plain", "application/json", "application/geo+json"];

    public $priority = 1;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        list($format, $type, $mimeType) = TypeResolver::identify($path);

        if ($format === GeoFormat::GEOJSON) {
            return new TypeIdentification($mimeType, DocumentType::GEODATA);
        }

        throw UnsupportedFileException::path($path);
    }
}