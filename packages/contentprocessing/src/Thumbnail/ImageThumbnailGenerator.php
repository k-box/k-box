<?php

namespace KBox\Documents\Thumbnail;

use KBox\File;
use KBox\Documents\DocumentType;
use KBox\Documents\Contracts\ThumbnailGenerator;

/**
 * Image Thumbnail Generator
 *
 * Generate thumbnail of image files
 */
class ImageThumbnailGenerator implements ThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {
        return ThumbnailImage::load($file->absolute_path)->orientate()->widen(ThumbnailImage::DEFAULT_WIDTH);
    }

    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes()) && $file->document_type === DocumentType::IMAGE;
    }

    public function supportedMimeTypes()
    {
        // tiff, BMP, ICO, PSD, are only supported if InterventionImage is using the imagemagik driver

        return [
            'image/png',
            'image/gif',
            'image/jpg',
            'image/jpeg',
        ];
    }
}
