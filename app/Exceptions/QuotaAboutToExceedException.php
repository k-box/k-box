<?php

namespace KBox\Exceptions;

use Exception;
use KBox\Facades\UserQuota;

final class QuotaAboutToExceedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     * @param  \Exception  $previous
     * @param  int  $code
     * @return void
     */
    public function __construct($message = null, Exception $previous = null, $code = 507)
    {
        parent::__construct($message ?? trans('errors.quota.not_enough_free_space', ['free' => human_filesize(UserQuota::free()), 'quota' => human_filesize(UserQuota::maximum())]), $code, $previous);
    }
}
