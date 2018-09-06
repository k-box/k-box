<?php

namespace KBox\Geo\Thumbnails;

use Imagick;
use KBox\File;
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

        $image = new Imagick();
        $image->setBackgroundColor('white'); // do not create transparent thumbnails
        $image->setResolution(300, 300); // forcing resolution to 300dpi prevents mushy images
        @$image->readImage($file->absolute_path); // file.pdf[0] refers to the first page of the pdf
        $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $image->thumbnailImage(ThumbnailImage::DEFAULT_WIDTH, 0, false, true);
        $image->setImageFormat("png");

        return ThumbnailImage::load($image)->widen(ThumbnailImage::DEFAULT_WIDTH);
    }

    public function isSupported(File $file)
    {
        if (! $this->isImagickSupportAvailable()) {
            return false;
        }
        return in_array($file->mime_type, $this->supportedMimeTypes()) && ($file->document_type === DocumentType::IMGE || $file->document_type === DocumentType::GEODATA);
    }

    public function supportedMimeTypes()
    {
        if (! $this->isImagickSupportAvailable()) {
            return [];
        }

        return [
            'image/tiff',
        ];
    }

    private function isImagickSupportAvailable()
    {
        if (! extension_loaded('imagick') && ! class_exists('Imagick')) {
            return false;
        }

        try {
            if (empty(Imagick::queryFormats("TIFF"))) {
                return false;
            }
        } catch (FatalErrorException $ex) {
            return false;
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }
}