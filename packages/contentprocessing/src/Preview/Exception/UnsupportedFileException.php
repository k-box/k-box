<?php

namespace Content\Preview\Exception;

use Exception;

/**
 * The file is not supported by the preview service
 */
class UnsupportedFileException extends Exception
{

    /**
     * Create a new exception
     *
     * @param string $message
     * @param Exception $previous
     * @return UnsupportedFileException
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 40001, $previous);
    }
}
