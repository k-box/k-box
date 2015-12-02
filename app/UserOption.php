<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

class UserOption extends Model {
    /*
    id: bigIncrements
    users: User,
    key
    value
    */



    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_options';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    public function user(){
        
        return $this->hasOne('User');

    }


    public function scopeOption($query, $key){
        return $query->whereKey($key);
    }



}
