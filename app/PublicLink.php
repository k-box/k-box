<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;
use KBox\Traits\LocalizableDateFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * PublicLink. A type target of a share.
 *
 * This represents a share with a public URL that do not
 * require to be an authenticated user to see.
 *
 * @property integer $id The unique identifier of this link
 * @property string $slug The human friendly identifier. Default null.
 * @property string $url The computed URL to be used by users to open the
 *                       resource pointed by the PublicLink
 * @property Carbon\Carbon created_at when the link was created
 * @property Carbon\Carbon updated_at when the link was lastly updated
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $user_id
 * @property-read \KBox\Shared $share
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\PublicLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PublicLink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PublicLink whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PublicLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PublicLink whereUserId($value)
 * @mixin \Eloquent
 */
class PublicLink extends Model
{
    use LocalizableDateFields;
    use HasFactory;

    protected $table = 'publiclinks';

    protected $fillable = ['user_id','slug'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * The user that created the public link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\KBox\User::class, 'user_id', 'id');
    }

    /**
     * The share to which this link refers to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne|KBox\Shared
     */
    public function share()
    {
        return $this->morphOne(\KBox\Shared::class, 'sharedwith');
    }

    /**
     * Check if the link is expired
     *
     * The link expires if related share is expired
     *
     * @see Shared::isExpired()
     * @return boolean
     */
    public function isExpired()
    {
        return $this->share->isExpired();
    }

    /**
     * Get the public url to reach this link.
     *
     * @return bool
     */
    public function getUrlAttribute()
    {
        return route('publiclinks.show', [
            'link' => ! is_null($this->slug) ? $this->slug : $this->share->token
        ]);
    }
}
