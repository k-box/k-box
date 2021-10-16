<?php

namespace KBox;

use KBox\Facades\Quota;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use KBox\Notifications\QuotaFullNotification;
use KBox\Notifications\QuotaLimitNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int|null $limit the storage quota reserved for the user (in bytes). Default null
 * @property boolean $unlimited if the user do not have a quota limit
 * @property int $used the amount of used storage space
 * @property int $free the amount of remaining free storage space
 * @property int $threshold the percentage of used storage after which the first notification should be sent
 * @property int $used_percentage the percentage of used storage space
 * @property boolean $notified if the user has been notified after reaching the threshold
 * @property boolean $notified_full if the user has been notified about reaching the capacity of the quota
 * @property boolean $is_above_threshold if the user has been notified about reaching the capacity of the quota
 * @property boolean $is_full if the user has been notified about reaching the capacity of the quota
 * @property \Carbon\Carbon $notification_sent_at when the threshold based notification was sent
 * @property \Carbon\Carbon $full_notification_sent_at when the full quota notification was sent
 * @property \KBox\User $user the assignee of the quota
 */
class UserQuota extends Model
{
    use HasFactory;
    
    const FULL_THRESHOLD = 99;
    
    protected $dates = [
        'created_at',
        'updated_at',
        'notification_sent_at',
        'full_notification_sent_at',
    ];

    protected $fillable = ['user_id', 'used', 'limit', 'threshold'];

    /**
     * The assignee of this quota
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include not notified quota threshold reached.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotNotified($query)
    {
        return $query->whereNull('notification_sent_at')->orWhereNotNull('full_notification_sent_at');
    }

    /**
     * Scope a query to only include not notified quota threshold reached.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \KBox\User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Set if the user has been notified
     *
     * @param  bool  $notified
     * @return void
     */
    public function setNotifiedAttribute($notified)
    {
        if ($notified && ! $this->notification_sent_at) {
            $this->attributes['notification_sent_at'] = Carbon::now();
        }

        if (! $notified && $this->notification_sent_at) {
            $this->attributes['notification_sent_at'] = null;
        }
    }

    /**
     * Get if the threshold pass was notified
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getNotifiedAttribute($value = null)
    {
        return isset($this->attributes['notification_sent_at']) &&
               ! is_null($this->attributes['notification_sent_at']);
    }

    /**
     * Set if the user has been notified
     *
     * @param  bool  $notified
     * @return void
     */
    public function setNotifiedFullAttribute($notified)
    {
        if ($notified && ! $this->full_notification_sent_at) {
            $this->attributes['full_notification_sent_at'] = Carbon::now();
        }

        if (! $notified && $this->full_notification_sent_at) {
            $this->attributes['full_notification_sent_at'] = null;
        }
    }

    /**
     * Get if the threshold pass was notified
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getNotifiedFullAttribute($value = null)
    {
        return isset($this->attributes['full_notification_sent_at']) &&
               ! is_null($this->attributes['full_notification_sent_at']);
    }

    /**
     * @return int|null
     */
    public function getLimitAttribute($value = null)
    {
        $current = $value ?? Quota::limit();

        if ($current <= 0) {
            return 0;
        }

        $unlimited = $this->attributes['unlimited'] ?? false;

        return $unlimited ? null : $current;
    }
    
    /**
     * @return bool
     */
    public function getUnlimitedAttribute($value)
    {
        if (! is_null($value)) {
            return $value;
        }

        if (is_null($value)) {
            return Quota::isUnlimited();
        }
        
        return false;
    }

    /**
     * @return int
     */
    public function getThresholdAttribute($value = null)
    {
        $threshold = $value ?? Quota::threshold();

        if ($threshold <= 0) {
            return 0;
        }

        return (int)$threshold;
    }
    
    /**
     * Get if the threshold pass was notified
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getIsAboveThresholdAttribute($value = null)
    {
        $percentage = $this->used_percentage;
        return $percentage >= $this->threshold;
    }
    
    public function getUsedPercentageAttribute($value = null)
    {
        if ($this->unlimited) {
            return 0;
        }

        if ($this->limit === 0) {
            return 100;
        }

        return round($this->used * 100 / $this->limit, 1);
    }
    
    public function getFreeAttribute($value = null)
    {
        if ($this->unlimited) {
            return null;
        }

        if ($this->limit === 0) {
            return 0;
        }

        return $this->limit - $this->used;
    }
    
    /**
     * Get if the threshold pass was notified
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getIsFullAttribute($value = null)
    {
        return $this->used_percentage >= self::FULL_THRESHOLD;
    }

    /**
     * Notify the user of quota usage, if needed
     */
    public function notify()
    {
        if (is_null($this->user)) {
            return;
        }

        if ($this->is_full && ! $this->notified_full) {
            $this->user->notify(new QuotaFullNotification($this));
            $this->notified_full = true;
        } elseif ($this->is_above_threshold && ! $this->notified_full && ! $this->notified) {
            $this->user->notify(new QuotaLimitNotification($this));
            $this->notified = true;
        }
    }

    /**
     * Calculate the user used quota and notify if above thresholds
     *
     * This method save the updated model on the database.
     *
     * @return \KBox\UserQuota
     */
    public function calculate()
    {
        $sum = File::whereUserId($this->user->id)->sum('size');
        
        $this->used = $sum;

        if (! $this->is_full && $this->notified_full) {
            $this->notified_full = false;
        }

        if (! $this->is_above_threshold && $this->notified) {
            $this->notified = false;
        }

        $this->notify();

        $this->save();

        return $this;
    }

    /**
     * Test if a new file with the specified size can fit the free space
     */
    public function accept($size)
    {
        if ($this->unlimited) {
            return true;
        }
        
        return $this->free > $size;
    }
}
