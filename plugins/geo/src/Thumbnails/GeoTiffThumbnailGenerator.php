<?php

namespace KBox\Geo\Thumbnails;

use Log;
use Imagick;
use KBox\File;
use ImagickException;
use KBox\Geo\Gdal\Gdal;
use KBox\Documents\DocumentType;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Contracts\ThumbnailGenerator;
use Symfony\Component\Debug\Exception\FatalErrorException;

class GeoTiffThumbnailGenerator implements ThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {
        if (! $this->isImagickSupportAvailable()) {
            throw new Exception('Failed to generate tiff thumbnail: imagemagick is not installed');
        }

        $png = (new Gdal())->convertRaster($file->absolute_path, GDAL::FORMAT_PNG, [
            '-ot Byte',
            '-scale',
            '-outsize 300 0'
        ]);

        try{
            $image = new Imagick();
            $image->setBackgroundColor('white');
            $image->setResolution(300, 300);
            @$image->readImage($png->getRealPath());
            $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            $image->thumbnailImage(ThumbnailImage::DEFAULT_WIDTH, 0, false, true);
            $image->setImageFormat("png");
        } catch (ImagickException $ex){

            throw new Exception("Failed to generate geotiff thumbnail. {$ex->getMessage()}");

        } finally {
            @unlink($png->getRealPath());
        }

        return ThumbnailImage::load($image)->widen(ThumbnailImage::DEFAULT_WIDTH);
    }

    public function isSupported(File $file)
    {
        if (! $this->isImagickSupportAvailable()) {
            return false;
        }

        if (! (new Gdal())->isInstalled()) {
            return false;
        }
        
        return in_array($file->mime_type, $this->supportedMimeTypes()) && $file->document_type === DocumentType::GEODATA;
    }

    public function supportedMimeTypes()
    {
        return [
            'image/tiff',
        ];
    }

    private function isImagickSupportAvailable()
    {
        if (! extension_loaded('imagick') && ! class_exists('Imagick')) {
            Log::warning('imagemagick class not found');
            return false;
        }

        try {
            if (empty(Imagick::queryFormats("TIFF"))) {
                return false;
            }
        } catch (FatalErrorException $ex) {
            Log::error('imagemagick support check for tiff', [$ex]);
            return false;
        } catch (Exception $ex) {
            Log::error('imagemagick support check for tiff', [$ex]);
            return false;
        }

        return true;
    }
}