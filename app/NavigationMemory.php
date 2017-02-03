<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 */
class NavigationMemory extends Model {
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


    public function navigationMemory(){
        
        // One to One
        return $this->hasOne('NavigationMemory');

        // One to Many
        // return $this->hasMany('NavigationMemory');
        // return $this->hasMany('NavigationMemory', 'parent', 'id');
	    
        // Many to Many
        // return $this->belongsToMany('NavigationMemory');
        // return $this->belongsToMany('NavigationMemory', 'PIVOT_TABLE'); //last is pivot table name

    }


}
