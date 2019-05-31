<?php

namespace KBox;

use KBox\User;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class PersonalExport extends Model
{
    protected $fillable = [
        'user_id', 'name'
    ];

    protected $casts = [
        'purge_at' => 'datetime',
        'generated_at' => 'datetime',
    ];

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
            'name' => "e-$uid-$uuid.zip"
        ]);
    }
}
