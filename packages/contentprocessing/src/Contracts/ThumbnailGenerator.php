<?php

namespace KBox\Documents\Contracts;

use KBox\File;
use KBox\Documents\Thumbnail\ThumbnailImage;

/**
 * Thumbnail generator interface.
 *
 * Define what methods must be exposed by a class that is able to
 * generate a thumbnail of a given file.
 * Usually a thumbnail is an image that represents the content of a given file
 */
interface ThumbnailGenerator
{
    /**
     * Generate a thumbnail
     *
     * @param KBox\File $file the file to generate the thumbnail for
     * @return KBox\Documents\Thumbnail\ThumbnailImage the thumbnail image instance
     */
    public function generate(File $file) : ThumbnailImage;

    /**
     * Check if a given File is supported by the thumbnail generator
     *
     * @param KBox\File $file the file to check
     * @return bool
     */
    public function isSupported(File $file);

    /**
     * The list of supported mime types that
     * the generator is able to process
     *
     * @return array
     */
    public function supportedMimeTypes();
}
