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
     * @param  \KBox\UserQuota  $quota The quota status
     * @param  int  $filesize the size of the file being uploaded
     * @param  \Exception  $previous
     * @param  int  $code
     * @return void
     */
    public function __construct(UserQuota $quota, $filesize, Exception $previous = null, $code = 507)
    {
        parent::__construct(
            trans('quota.not_enough_free_space', [
                'necessary_free_space' => human_filesize($filesize - $quota->free),
                'quota' => human_filesize($quota->limit)
            ]),
            $code,
            $previous
        );
    }

    public static function user(User $user, $filesize)
    {
        return new self(Quota::user($user), $filesize);
    }
}
