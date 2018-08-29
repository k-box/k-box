<?php

namespace KBox\Documents\Exceptions;

use Exception;
use KBox\File;

/**
 * No thumbnail generator has been found
 */
class ThumbnailGeneratorNotFoundException extends Exception
{

    /**
     * Create a new exception
     *
     * @param string $message
     *
     * @param Exception $previous
     * @return UnsupportedFileException
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 40002, $previous);
    }

    /**
     * Create a new thumbnail generator not found for the given File
     *
     * @param KBox\File $file
     * @return ThumbnailGeneratorNotFoundException
     */
    public static function for(File $file)
    {
        return new self("No thumbnail generator registered for mime type [{$file->mime_type}] and document type [{$file->document_type}]");
    }
}
