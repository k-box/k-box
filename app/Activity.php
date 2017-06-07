<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

/**
 * KlinkDMS\Activity
 *
 * @deprecated 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $instance_id
 * @property string $instance_type
 * @property int $user_id
 * @property-read \KlinkDMS\User $user
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Activity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Activity whereInstanceId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Activity whereInstanceType($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\Activity whereUserId($value)
 * @mixin \Eloquent
 */
class Activity extends Model {
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


    public function user(){
        
        return $this->hasOne('KlinkDMS\User');

    }


}
