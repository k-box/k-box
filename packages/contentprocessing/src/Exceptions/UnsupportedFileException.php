<?php

namespace KBox\Documents\Exceptions;

use Exception;
use KBox\File;

/**
 * The file is not supported the service
 */
class UnsupportedFileException extends Exception
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
        parent::__construct($message, 40001, $previous);
    }

    /**
     * Create a new Unsupported file exception for the given File
     *
     * @param KBox\File $file
     * @return UnsupportedFileException
     */
    public static function file(File $file)
    {
        return new self("The file [{$file->name}] with mime type [{$file->mime_type}] is not supported for thumbnail generation");
    }
}
