<?php

namespace KBox;

use Ramsey\Uuid\Uuid;
use KBox\Traits\LocalizableDateFields;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class PersonalExport extends Model
{
    use LocalizableDateFields;

    protected $fillable = [
        'user_id', 'name', 'purge_at',
    ];

    protected $casts = [
        'purge_at' => 'datetime',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Filter for user specific exports
     *
     * @param \KBox\User|int $user
     */
    public function scopeOfUser($query, $user)
    {
        return $query->where('user_id', is_a($user, User::class) ? $user->id : $user);
    }

    /**
     * Filter personal export created, but not completed
     */
    public function scopePending($query)
    {
        return $query->whereNull('generated_at');
    }

    /**
     * Filter for expired exports.
     *
     * An export is expired if purge_at is less than
     * the current date and time
     *
     * @var
     */
    public function scopeExpired($query)
    {
        return $query->where('purge_at', '<', now());
    }

    /**
     * Filter for not expired exports.
     *
     * An export is expired if purge_at is less than
     * the current date and time
     *
     * @var
     */
    public function scopeNotExpired($query)
    {
        return $query->where('purge_at', '>=', now());
    }

    /**
     * Delete the export archive and the export entry
     */
    public function purge()
    {
        $storage = Storage::disk(config('personal-export.disk'));
        @$storage->delete($this->name);

        return $this->delete();
    }

    /**
     * Check if the export is still downloadable
     */
    public function isExpired()
    {
        return $this->purge_at->lessThan(now());
    }
    
    /**
     * Check if the export is still downloadable
     */
    public function isPending()
    {
        return is_null($this->generated_at);
    }

    public function getPurgeAt($full = false)
    {
        if (is_null($this->purge_at)) {
            return "";
        }

        return $this->getLocalizedDateInstance($this->purge_at)->format(trans($full ? 'units.date_format_full' : 'units.date_format'));
    }

    /**
     * Create a new data export
     *
     * @param User $user
     */
    public static function requestNewExport(User $user)
    {
        $uuid = Uuid::uuid4()->toString();

        $uid = is_a($user, User::class) ? $user->id : $user;

        return static::create([
            'user_id' => $uid,
            'name' => "e-$uid-$uuid.zip",
            'purge_at' => now()->addDays(config('personal-export.delete_after_days')),
        ]);
    }
}
