<?php

namespace Content\Services;

use Content\ExtractText\ExtractTextFactory;

/**
 * The service that can generate a document Preview.
 *
 * This class is only used to expose the static PreviewFactory as a dependency injection service
 */
class TextExtractionService
{

    /**
     * Load a file and return the correspondent text extractor
     *
     * @param string $path the path of the file
     * @param string $extension (optional) The file extension, if cannot be deducted from the $path.
     *                         If specified will be used to find the correct preview renderer
     * @return Content\Contract\ExtractText
     * @throws TextExtractionException if an error occurred
     * @throws UnsupportedFileException if the file type is not supported
     */
    public function load($path, $extension = null)
    {
        return ExtractTextFactory::load($path, $extension);
    }

    /**
     * Check if a file is supported by the preview system
     *
     * @param string $path the path of the file
     * @return bool true if the file is supported by the preview service, false otherwise
     */
    public function isFileSupported($path)
    {
        return ExtractTextFactory::isFileSupported($path);
    }
}
