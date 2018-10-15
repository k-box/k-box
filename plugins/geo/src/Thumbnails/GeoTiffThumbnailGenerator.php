<?php

namespace KBox\Geo\Thumbnails;

use Log;
use Imagick;
use KBox\File;
use Exception;
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

        if($this->requiresBandStretching($file)){
            // the image consists in a grey scale image with band values not in the Byte [0,255] range
            // we make sure that band values are then scaled so the thumbnail will be closed to the image preview
            try{
                $png = (new Gdal())->convertRaster($file->absolute_path, GDAL::FORMAT_PNG, [
                    '-ot Byte',
                    '-scale',
                    '-outsize 300 0'
                ]);

                return $this->thumbnailUsingImagick($png->getRealPath());

            } catch (Exception $ex){

                Log::error("Failed to re-scale geotiff for thumbnail generation", ['ex' => $ex]);

                return $this->thumbnailUsingImagick($file->absolute_path);
    
            } finally {
                @unlink($png->getRealPath());
            }
        }

        return $this->thumbnailUsingImagick($file->absolute_path);
    }

    /**
     * Check the bands metadata to see if there is at least one band whose type is not Byte
     */
    private function requiresBandStretching($file)
    {
        $bands = $file->properties->bands ?? [];

        // check if there are bands with type different than Byte
        $filtered_bands = array_filter($bands, function($band){
            return isset($band['type']) && $band['type'] !== 'Byte';
        });

        return !empty($filtered_bands);
    }

    private function thumbnailUsingImagick($file_path)
    {
        try{
            $image = new Imagick();
            $image->setBackgroundColor('white');
            $image->setResolution(300, 300);
            @$image->readImage($file_path);
            $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            $image->thumbnailImage(ThumbnailImage::DEFAULT_WIDTH, 0, false, true);
            $image->setImageFormat("png");
            
            return ThumbnailImage::load($image)->widen(ThumbnailImage::DEFAULT_WIDTH);

        } catch (ImagickException $ex){

            throw new Exception("Failed to generate geotiff thumbnail. {$ex->getMessage()}");

        }

    }

    public function isSupported(File $file)
    {
        if (! $this->isImagickSupportAvailable()) {
            return false;
        }
        return in_array($file->mime_type, $this->supportedMimeTypes()) && ($file->document_type === DocumentType::IMAGE || $file->document_type === DocumentType::GEODATA);
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