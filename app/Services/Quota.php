<?php

namespace KBox\Services;

use KBox\User;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KBox\File;

class Quota
{
    const UNLIMITED = INF;

    const OPTION_QUOTA = 'quota';

    private $user;

    public function __construct(AuthGuard $auth)
    {
        $this->user = $auth->user();
    }

    public function withUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the currently configured default user storage quota
     *
     * @return int|null
     */
    public function defaultQuota()
    {
        return config('quota.user_storage_default');
    }

    public function isUnlimited()
    {
        return is_infinite($this->maximum());
    }
    
    /**
     * Check whether the user quota can contain
     * the specified additional capacity
     *
     * @param int $size
     * @return boolean
     */
    public function accept($size)
    {
        $free = $this->free();

        if (is_infinite($free)) {
            return true;
        }

        return $free >= $size;
    }
    
    public function used()
    {
        return File::whereUserId($this->user->id)->sum('size');
    }
    
    public function free()
    {
        $max = $this->maximum();

        if ($this->isUnlimited()) {
            return INF;
        }

        if ($max <= 0) {
            return 0;
        }

        return $max - $this->used();
    }
    
    public function maximum()
    {
        $quota = $this->defaultQuota();

        if ($this->user) {
            $quota = $this->getUserConfiguration() ?? $quota;
        }

        if (is_null($quota)) {
            return self::UNLIMITED;
        }

        return $quota <= 0 ? 0 : $quota;
    }

    private function getUserConfiguration()
    {
        return optional($this->user->options()->option(self::OPTION_QUOTA)->first())->value;
    }
}
