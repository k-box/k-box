<?php

namespace KBox\Services;

use KBox\User;
use KBox\UserQuota;

/**
 * Quota configuration.
 *
 * Read the default quota configuration limit and threshold
 */
class Quota
{
    const UNLIMITED = INF;
    const DEFAULT_THRESHOLD = 80;

    /**
     * Get the currently configured user storage quota limit
     *
     * @return int|null
     */
    public function limit()
    {
        $quota = config('quota.user');
    
        if (\is_null($quota)) {
            return self::UNLIMITED;
        }

        if (\is_bool($quota) || \is_object($quota) || \is_array($quota)) {
            return 0;
        }

        if (\is_string($quota)) {
            $quota = \intval($quota, 10);
        }

        return $quota <= 0 ? 0 : $quota;
    }

    /**
     * Default threshold for first quota notification
     *
     * @return int the percentage of used space that must be filled before sending the notification
     */
    public function threshold()
    {
        $threshold = config('quota.threshold') ?? self::DEFAULT_THRESHOLD;

        if (\is_bool($threshold) || \is_object($threshold) || \is_array($threshold)) {
            return self::DEFAULT_THRESHOLD;
        }

        if (\is_string($threshold)) {
            $threshold = \intval($threshold, 10);
        }

        return $threshold <= 0 ? 0 : $threshold;
    }

    /**
     * Check whether the default quota for users is unlimited
     *
     * @return bool
     */
    public function isUnlimited()
    {
        return is_infinite(self::limit());
    }
    
    /**
     * Check whether the user quota can contain
     * the specified file size
     *
     * @param \KBox\User $user
     * @param int $size
     * @return boolean
     */
    public function accept(User $user, $size)
    {
        return optional(self::user($user))->accept($size) ?? false;
    }
    
    /**
     * Retrieve the quota information for the given user
     *
     * @param \KBox\User
     * @return \KBox\UserQuota
     */
    public function user(User $user)
    {
        return UserQuota::firstOrCreate(['user_id' => $user->id]);
    }
}
