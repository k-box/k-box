<?php

namespace KBox\Exceptions;

use Exception;
use KBox\User;
use KBox\UserQuota;
use KBox\Facades\Quota;

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
    public function __construct(UserQuota $quota, Exception $previous = null, $code = 507)
    {
        parent::__construct(
            trans('quota.not_enough_free_space', [
                'free' => human_filesize($quota->free),
                'quota' => human_filesize($quota->limit)
            ]),
            $code,
            $previous
        );
    }

    public static function user(User $user)
    {
        return new self(Quota::user($user));
    }
}
