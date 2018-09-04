<?php

namespace KBox\Documents\Thumbnail;

use Imagick;
use KBox\File;
use KBox\Documents\DocumentType;
use KBox\Documents\Contracts\ThumbnailGenerator;
use Symfony\Component\Debug\Exception\FatalErrorException;

/**
 * Pdf Thumbnail Generator
 *
 * Generate thumbnail of a pdf file
 */
class PdfThumbnailGenerator implements ThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {
        if (! $this->isImagickSupportAvailable()) {
            throw new Exception('Failed to generate pdf thumbnail: imagemagick is not installed');
        }

        $image = new Imagick();
        $image->setBackgroundColor('white'); // do not create transparent thumbnails
        $image->setResolution(300, 300); // forcing resolution to 300dpi prevents mushy images
        @$image->readImage($file->absolute_path.'[0]'); // file.pdf[0] refers to the first page of the pdf
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
        return in_array($file->mime_type, $this->supportedMimeTypes()) && $file->document_type === DocumentType::PDF_DOCUMENT;
    }

    public function supportedMimeTypes()
    {
        if (! $this->isImagickSupportAvailable()) {
            return [];
        }

        return [
            'application/pdf',
        ];
    }

    private function isImagickSupportAvailable()
    {
        if (! extension_loaded('imagick') && ! class_exists('Imagick')) {
            return false;
        }

        try {
            if (empty(Imagick::queryFormats("PDF"))) {
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
