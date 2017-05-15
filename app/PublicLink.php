<?php

namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use KlinkDMS\Traits\LocalizableDateFields;

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
 */
class PublicLink extends Model
{
    use LocalizableDateFields;

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
        return $this->belongsTo('KlinkDMS\User', 'user_id', 'id');
    }


    /**
     * The share to which this link refers to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne|KlinkDMS\Shared
     */
    public function share()
    {
        return $this->morphOne('KlinkDMS\Shared', 'sharedwith');
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
            'link' => !is_null($this->slug) ? $this->slug : $this->share->token
        ]);
    }
}
