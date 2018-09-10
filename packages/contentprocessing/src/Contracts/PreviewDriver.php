<?php

namespace KBox\Documents\Contracts;

use KBox\File;
use Illuminate\Contracts\Support\Renderable;

/**
 * Preview Driver interface.
 *
 * Define what methods must be exposed by a preview generation class.
 * A preview class converts the content of a specific file type to HTML
 */
interface PreviewDriver
{
    /**
     * Creates a File preview into a renderable object
     *
     * @param KBox\File $file the file to generate the preview for
     * @return KBox\Documents\Contracts\Previewable
     */
    public function preview(File $file) : Renderable;

    /**
     * Check if a given File is supported by the preview driver
     *
     * @param KBox\File $file the file to check
     * @return bool
     */
    public function isSupported(File $file);

    /**
     * The list of supported mime types
     *
     * @return array
     */
    public function supportedMimeTypes();
}
