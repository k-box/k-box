<?php

namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use KlinkDMS\Traits\LocalizableDateFields;

use Carbon\Carbon;

/**
 * KlinkDMS\Shared
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $shareable_id
 * @property string $shareable_type
 * @property \Carbon\Carbon $expiration
 * @property int $sharedwith_id
 * @property string $sharedwith_type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $shareable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $sharedwith
 * @property-read \KlinkDMS\User $user
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared by($user)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared byWithWhat($user, $with, $what)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared expired()
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared notExpired()
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared sharedByMe($user)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared sharedWithGroup($user)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared sharedWithGroups($group_ids)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared sharedWithMe($user)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared token($token)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereExpiration($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereShareableId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereShareableType($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereSharedwithId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereSharedwithType($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Shared whereUserId($value)
 * @mixin \Eloquent
 */
class Shared extends Model
{
    use LocalizableDateFields;
    /*
    id: bigIncrements
    created_at: date
    updated_at: date
    token: string
    sharable_id: id
    sharable_type: string
    expiration: dateTime
    user_id: index
    shared_with: User
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shared';

    protected $fillable = ['user_id', 'sharable_id', 'sharable_type', 'token', 'sharedwith_id', 'sharedwith_type'];

    protected $dates = ['created_at', 'updated_at', 'expiration'];

    public function user()
    {
        
        // One to One
        return $this->belongsTo('KlinkDMS\User', 'user_id');
    }

    public function shareable()
    {
        return $this->morphTo();
    }
    
    public function sharedwith()
    {
        return $this->morphTo();
    }

    /**
     * Get shared by user
     *
     * @param  string|User $user the user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBy($query, $user)
    {
        if (class_basename(get_class($user)) === 'User') {
            $user = $user->id;
        }

        return $query->where('user_id', $user);
    }
    
    /**
     * Get shared by user, with who and what
     *
     * @param  User $user the user
     * @param  User|PeopleGroup $with the target of the share
     * @param  DocumentDescriptor|Group $what what has been shared
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWithWhat($query, $user, $with, $what)
    {
        if (class_basename(get_class($user)) === 'User') {
            $user = $user->id;
        }

        $with_class = get_class($with);
        $what_class = get_class($what);

        return $query->where('user_id', $user)
                     ->where('sharedwith_id', $with->id)
                     ->where('sharedwith_type', $with_class)
                     ->where('shareable_id', $what->id)
                     ->where('shareable_type', $what_class);
    }

    /**
     * Get shared with user
     *
     * @param  string|User $user the user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSharedWithMe($query, $user)
    {
        if (class_basename(get_class($user)) === 'User') {
            $user = $user->id;
        }

        return $query->where('sharedwith_id', $user)->where('sharedwith_type', 'KlinkDMS\User');
    }

    public function scopeSharedByMe($query, $user)
    {
        if (class_basename(get_class($user)) === 'User') {
            $user = $user->id;
        }

        return $query->where('user_id', $user);
    }
    
    
    public function scopeSharedWithGroup($query, $user)
    {
        if (class_basename(get_class($user)) === 'PeopleGroup') {
            $user = $user->id;
        }

        return $query->where('sharedwith_id', $user)->where('sharedwith_type', 'KlinkDMS\PeopleGroup');
    }
    
    public function scopeSharedWithGroups($query, $group_ids)
    {
        return $query->whereIn('sharedwith_id', $group_ids)->where('sharedwith_type', 'KlinkDMS\PeopleGroup');
    }

    /**
     * Get all the expired sharing
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration', '<=', Carbon::now());
    }
    
    /**
     * Get all the valid sharing
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expiration', '>', Carbon::now());
    }

    /**
     * Filter from the share token
     *
     * @param  string $token
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToken($query, $token)
    {
        return $query->whereToken($token);
    }

    /**
     * Check if the share is expired.
     *
     * The share expires if the current date is greater than the expiration date
     *
     * @return boolean
     */
    public function isExpired()
    {
        if (! is_null($this->expiration)) {
            return Carbon::now()->gt($this->expiration);
        }
        return false;
    }

    /**
     * Tell if the share is with a {@see PublicLink}
     *
     * @return bool
     */
    public function isPublicLink()
    {
        return $this->sharedwith_type === 'KlinkDMS\PublicLink';
    }
}
