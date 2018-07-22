<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\NavigationMemory
 *
 * @deprecated
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $parent
 * @property int $user_id
 * @property-read \KBox\NavigationMemory $navigationMemory
 * @method static \Illuminate\Database\Query\Builder|\KBox\NavigationMemory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\NavigationMemory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\NavigationMemory whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\NavigationMemory whereParent($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\NavigationMemory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\NavigationMemory whereUserId($value)
 * @mixin \Eloquent
 */
class NavigationMemory extends Model
{
    /*
    id: bigIncrements
    name: string
    navigation_memories: NavigationMemory
    users: User
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'navigation_memories';

    public function navigationMemory()
    {
        
        // One to One
        return $this->hasOne(\KBox\NavigationMemory::class);

        // One to Many
        // return $this->hasMany('NavigationMemory');
        // return $this->hasMany('NavigationMemory', 'parent', 'id');
        
        // Many to Many
        // return $this->belongsToMany('NavigationMemory');
        // return $this->belongsToMany('NavigationMemory', 'PIVOT_TABLE'); //last is pivot table name
    }
}
