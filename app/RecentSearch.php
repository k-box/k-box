<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * Represent a recent search of a user
 *
 * Fields:
 * - id: bigIncrements
 * - terms: string
 * - times: bigInteger
 * - user_id: User
 *
 * @property int $id
 * @property string $terms
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $times
 * @property int $user_id
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch ofUser($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch thatContains($term)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch whereTerms($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch whereTimes($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RecentSearch whereUserId($value)
 * @mixin \Eloquent
 */
class RecentSearch extends Model
{

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['terms'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recent_searches';

    /**
     * The user that performed the search
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(\KBox\User::class);
    }

    /**
     * Scope the query to contain only searches of a
     * specific user
     *
     * @param string|int $user_id the user ID (primary key)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope the query to contain only searches that
     * contains a search term.
     *
     * The comparison is done searching the term inside the all
     * terms string for the search.
     *
     * @param string $term the terms to retrieve the searches for
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThatContains($query, $term)
    {
        return $query->where('terms', 'like', '%'.$term.'%');
    }
}
