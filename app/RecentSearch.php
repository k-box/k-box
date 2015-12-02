<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model {
    /*
    id: bigIncrements
    terms: string
    times: bigInteger
    user_id: User
    */

    protected $fillable = ['terms'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recent_searches';


    public function user(){
        
        // One to One
        return $this->belongsTo('User');

    }


    public function scopeOfUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }


    public function scopeThatContains($query, $term)
    {
        return $query->where('terms', 'like', '%'. $term .'%');
    }
    

}
