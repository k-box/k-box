<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\Activity
 *
 * @deprecated
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $instance_id
 * @property string $instance_type
 * @property int $user_id
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Activity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Activity whereInstanceId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Activity whereInstanceType($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Activity whereUserId($value)
 * @mixin \Eloquent
 */
class Activity extends Model
{
    /*
    id: bigIncrements
    instance: morphs
    users: User
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activities';

    public function user()
    {
        return $this->hasOne('KBox\User');
    }
}
