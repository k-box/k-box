<?php

namespace KBox\Documents\Exceptions;

use Exception;
use KBox\File;

/**
 * If a driver, with the specific characteristics, cannot be found
 */
class DriverNotFoundException extends Exception
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
        parent::__construct($message, 80001, $previous);
    }

    /**
     * Create a new Unsupported file exception for the given File
     *
     * @param KBox\File $file
     * @return DriverNotFoundException
     */
    public static function for(File $file)
    {
        return new self("A driver for [{$file->mime_type}, [{$file->document_type}]] cannot be found.");
    }
}
