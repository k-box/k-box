<?php

namespace KBox\Geo;

use SplFileInfo;

use KBox\File;
use OneOffTech\GeoServer\GeoFile as BaseGeoFile;
use KBox\Geo\Support\TypeResolver;
use OneOffTech\GeoServer\Exception\UnsupportedFileException;

/**
 * A Geographic file.
 * 
 * It is used to wrap a file given its path in a structure that describe the file itself for processing the upload
 */
class GeoFile extends BaseGeoFile
{
    public function __construct($path)
    {
        $this->file = new SplFileInfo($path);

        list($format, $type, $mimeType) = TypeResolver::identify($path);

        if (!in_array($format, TypeResolver::supportedFormats())) {
            throw new UnsupportedFileException($path, $format, join(', ', TypeResolver::supportedFormats()));
        }

        $this->mimeType = $mimeType;
        
        $this->extension = $this->file->getExtension();
        
        $this->normalizedExtension = TypeResolver::normalizedExtensionFromFormat($format) ?? $this->extension;
        
        $this->normalizedMimeType = TypeResolver::normalizedMimeTypeFromFormat($format) ?? $mimeType;

        $this->format = $format;
        
        $this->type = $type;

        $this->name = $this->originalName = $this->file->getFileName();
    }

    public function content()
    {
        return file_get_contents($this->file->getRealPath());
    }


    public function __get($property)
    {
        return $this->$property;
    }


    public function toArray()
    {
        return [
            $this->file,
            $this->mimeType,
            $this->extension,
            $this->normalizedExtension,
            $this->normalizedMimeType,
            $this->format,
            $this->type,
            $this->name,
        ];
    }

    public function __toString()
    {
        return join(',', $this->toArray());
    }

    /**
     * Create a Geo file instance from a File instance
     *
     * @param string $path
     * @return Data
     * @throws UnsupportedFileException if file is not supported
     */
    public static function fromFile(File $file)
    {
        $data = new static($file->absolute_path);

        $data->name($file->uuid);

        return $data;
    }


    /**
     * Check if the specified file is a valid geographical file
     *
     * @param string $path
     * @return bool
     */
    public static function isSupported(string $path)
    {
        list($format, $type, $mimeType) = TypeResolver::identify($path);
        return in_array($format, TypeResolver::supportedFormats());
    }
}
