<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
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
        
        // One to One
        return $this->hasOne('User');

        // One to Many
        // return $this->hasMany('User');
        // return $this->hasMany('User', 'user_id', 'id');
	    
        // Many to Many
        // return $this->belongsToMany('User');
        // return $this->belongsToMany('User', 'PIVOT_TABLE'); //last is pivot table name

    }


}
