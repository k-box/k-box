<?php

namespace KBox\Exceptions;

use Exception;

final class QuotaExceededException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     * @param  \Exception  $previous
     * @param  int  $code
     * @return void
     */
    public function __construct($message, Exception $previous = null, $code = 507)
    {
        parent::__construct($message, $code, $previous);
    }
}
