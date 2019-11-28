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

        // reset the internal iterator
        // if the image was generated from a multipage pdf resetting the iterator means
        // pointing to the first page
        $image->setResolution(300, 300);
        $image->resetIterator();

        // create a new imagick object to host only the first image in the sequence
        $thumb = new Imagick();

        // get the first image from the sequence and copy it in the current empty image
        $thumb->addImage($image->getImage());
        $image->clear();

        // ensure there will be a white background and
        // transparency is removed
        $thumb->setImageBackgroundColor('white');
        $thumb->setBackgroundColor('white');
        $thumb->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        $thumb->setResolution(300, 300); // forcing resolution to 300dpi prevents mushy images
        $thumb->thumbnailImage(ThumbnailImage::DEFAULT_WIDTH, 0, false, true);
        $thumb->setImageFormat("png");

        return ThumbnailImage::load($thumb)->widen(ThumbnailImage::DEFAULT_WIDTH);
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
