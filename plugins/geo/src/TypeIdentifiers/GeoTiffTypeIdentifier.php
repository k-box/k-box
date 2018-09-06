<?php

namespace KBox\Geo\TypeIdentifiers;

use KBox\Geo\GeoType;
use KBox\Geo\GeoFormat;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentifier;
use KBox\Geo\Support\TypeResolver;
use KBox\Documents\TypeIdentification;
use KBox\Documents\Exceptions\UnsupportedFileException;

class GeoTiffTypeIdentifier extends TypeIdentifier
{
    public $accept = ["image/tiff"];

    public $priority = 5;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        \Log::warning('geotiff identifier called', func_get_args());
        list($format, $type, $mimeType) = TypeResolver::identify($path);

        if ($format === GeoFormat::GEOTIFF) {
            return new TypeIdentification($mimeType, DocumentType::GEODATA);
        }

        throw UnsupportedFileException::path($path);
    }
}