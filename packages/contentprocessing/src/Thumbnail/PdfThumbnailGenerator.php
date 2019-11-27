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

        // PHP 7.1 64 bit on Windows: don't pass the file name to the constructor: it may break PHP.
        // Workaround for Imagick issue https://github.com/mkoppanen/imagick/issues/252
        // The workaround is part of the avalanche123/Imagine library (https://github.com/avalanche123/Imagine/blob/develop/src/Imagick/Imagine.php)
        // Copyright (c) 2004-2012 Bulat Shakirzyanov

        if (DIRECTORY_SEPARATOR === '\\' && PHP_INT_SIZE === 8 && PHP_VERSION_ID >= 70100 && PHP_VERSION_ID < 70200) {
            $image = new Imagick();
            
            $image->readImageBlob(@file_get_contents($file->absolute_path));
        } else {
            $image = new Imagick($file->absolute_path);
        }

        $image->setBackgroundColor('white'); // do not create transparent thumbnails
        $image->setResolution(300, 300); // forcing resolution to 300dpi prevents mushy images
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

    protected function isImagickSupportAvailable()
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
