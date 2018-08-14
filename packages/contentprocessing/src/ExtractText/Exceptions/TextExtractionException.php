<?php

namespace KBox\Documents\ExtractText\Exceptions;

use Exception;

/**
 * The file preview cannot be show due to an error
 * while processing the file
 */
class TextExtractionException extends Exception
{
    /**
     * Create a new exception
     *
     * @param string $message
     * @param Exception $previous
     * @return TextExtractionException
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 40002, $previous);
    }
}
