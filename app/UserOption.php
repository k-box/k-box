<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

/**
 * The User attached preferences
 *
 * Fields:
 * - id: bigIncrements
 * - user_id: User,
 * - key
 * - value
 */
class UserOption extends Model {


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_options';

    /**
     * Do not handle model dates
     */
    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    /**
     * The user to which apply the option
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {    
        return $this->hasOne('User');
    }

    /**
     * Scope a query to include only the option with the
     * specified key
     *
     * @param string $key the option key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOption($query, $key)
    {
        return $query->whereKey($key);
    }

}
