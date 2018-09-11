<?php

namespace KBox\Geo\Thumbnails;

use Exception;
use KBox\File;
use KBox\Geo\GeoService;
use KBox\Documents\DocumentType;
use OneOffTech\GeoServer\GeoFile;
use KBox\Documents\Thumbnail\ThumbnailImage;
use OneOffTech\GeoServer\Support\ImageResponse;
use KBox\Documents\Contracts\ThumbnailGenerator;
use KBox\Documents\Exceptions\UnsupportedFileException;
use Symfony\Component\Debug\Exception\FatalErrorException;

class ShapefileThumbnailGenerator implements ThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {
        $service = app(GeoService::class);
        
        if(! $service->isEnabled()){
            throw new Exception('Geographic integration not enabled');
        }

        if(! $service->isSupported($file)){
            throw UnsupportedFileException::file($file);
        }

        if(! $service->exist($file)){
            throw new Exception("Shapefile [$file->uuid] not uploaded to Geoserver. Thumbnail generation aborted.");
        }
        
        $response = $service->thumbnail($file);

        return ThumbnailImage::load($response->asString())->widen(ThumbnailImage::DEFAULT_WIDTH);
    }

    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes())
               && $file->document_type === DocumentType::GEODATA;
    }

    public function supportedMimeTypes()
    {
        return [
            'application/octet-stream',
        ];
    }
}