<?php

namespace KBox\Geo\Thumbnails;

use Log;
use Imagick;
use KBox\File;
use KBox\Documents\DocumentType;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Contracts\ThumbnailGenerator;
use KBox\Documents\Thumbnail\PdfThumbnailGenerator;
use Symfony\Component\Debug\Exception\FatalErrorException;

class GeoJsonGpxKmlThumbnailGenerator extends PdfThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {

        // Convert to PDF using GDAL
        // Convert PDF to PNG using Imagemagick

        if (! $this->isImagickSupportAvailable()) {
            throw new Exception('Failed to generate GeoJsonGpxKml thumbnail: imagemagick is not installed');
        }

        $pdf = (new Gdal())->convert($path, GDAL::FORMAT_PDF);

        $image = new Imagick();
        $image->setBackgroundColor('white');
        $image->setResolution(300, 300);
        @$image->readImage($pdf->getRealPath());
        $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $image->thumbnailImage(ThumbnailImage::DEFAULT_WIDTH, 0, false, true);
        $image->setImageFormat("png");

        unlink($pdf->getRealPath());

        return ThumbnailImage::load($image)->widen(ThumbnailImage::DEFAULT_WIDTH);

    }

    public function isSupported(File $file)
    {
        if (! $this->isImagickSupportAvailable()) {
            return false;
        }

        return in_array($file->mime_type, $this->supportedMimeTypes()) && $file->document_type === DocumentType::GEODATA;
    }

    public function supportedMimeTypes()
    {
        return [
            "application/gpx+xml",
            "application/geo+json",
            "application/vnd.google-earth.kml+xml",
            "application/vnd.google-earth.kmz"
        ];
    }

    
}