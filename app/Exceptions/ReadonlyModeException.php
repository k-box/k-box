<?php

namespace KBox\Exceptions;

use Exception;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class ReadonlyModeException extends MaintenanceModeException
{
    /**
     * Create a new exception instance.
     *
     * @param  int  $time
     * @param  int  $retryAfter
     * @param  string  $message
     * @param  \Exception  $previous
     * @param  int  $code
     * @return void
     */
    public function __construct($time, $retryAfter = null, $message = null, Exception $previous = null, $code = 0)
    {
        parent::__construct($time, $retryAfter, $message ?? trans('errors.503-readonly_text'), $previous, $code);
    }
}
