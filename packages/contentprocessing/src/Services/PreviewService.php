<?php

namespace KBox\Documents\Services;

use KBox\Documents\Preview\PreviewFactory;

/**
 * The service that can generate a document Preview.
 *
 * This class is only used to expose the static PreviewFactory as a dependency injection service
 */
class PreviewService
{

    /**
     * Load a file and return the correspondent preview renderer
     *
     * @param string $path the path of the file
     * @param string $extesion (optional) The file extension, if cannot be deducted from the $path.
     *                         If specified will be used to find the correct preview renderer
     * @return KBox\Documents\Contract\Preview
     * @throws PreviewGenerationException if an error occurred during the preview generation
     * @throws UnsupportedFileException if the file type is not supported
     */
    public function load($path, $extension = null)
    {
        return PreviewFactory::load($path, $extension);
    }

    /**
     * Check if a file is supported by the preview system
     *
     * @param string $path the path of the file
     * @return bool true if the file is supported by the preview service, false otherwise
     */
    public function isFileSupported($path)
    {
        return PreviewFactory::isFileSupported($path);
    }
}
