<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

class Shared extends Model {
    /*
    id: bigIncrements
    created_at: date
    updated_at: date
    token: string
    sharable_id: id
    sharable_type: string
    expiration: dateTime
    user_id: index
    shared_with: User
    */



    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shared';

    protected $fillable = ['user_id', 'sharable_id', 'sharable_type', 'token', 'sharedwith_id', 'sharedwith_type'];


    public function user(){
        
        // One to One
        return $this->belongsTo('KlinkDMS\User', 'user_id');

    }


    public function shareable()
    {
        return $this->morphTo();
    }
    
    public function sharedwith()
    {
        return $this->morphTo();
    }

    /**
     * Get shared by user
     * @param  [type] $query [description]
     * @param  [type] $user  [description]
     * @return [type]        [description]
     */
    public function scopeBy($query, $user)
    {
        if(class_basename(get_class($user)) === 'User'){
            $user = $user->id;
        }

        return $query->where('user_id', $user);
    }

    /**
     * Get shared with user
     * @param  [type] $query [description]
     * @param  [type] $user  [description]
     * @return [type]        [description]
     */
    public function scopeSharedWithMe($query, $user)
    {

        if(class_basename(get_class($user)) === 'User'){
            $user = $user->id;
        }

        return $query->where('sharedwith_id', $user)->where('sharedwith_type', 'KlinkDMS\User');
    }

    public function scopeSharedByMe($query, $user)
    {

        if(class_basename(get_class($user)) === 'User'){
            $user = $user->id;
        }

        return $query->where('user_id', $user);
    }
    
    
    public function scopeSharedWithGroup($query, $user)
    {

        if(class_basename(get_class($user)) === 'PeopleGroup'){
            $user = $user->id;
        }

        return $query->where('sharedwith_id', $user)->where('sharedwith_type', 'KlinkDMS\PeopleGroup');
    }
    
    public function scopeSharedWithGroups($query, $group_ids)
    {

        return $query->whereIn('sharedwith_id', $group_ids)->where('sharedwith_type', 'KlinkDMS\PeopleGroup');
    }

    /**
     * Get all the expired sharing
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration', '<=', Carbon::now());
    }

    /**
     * Filter from the share token
     * @param  [type] $query [description]
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function scopeToken($query, $token)
    {
        return $query->whereToken($token);
    }

}
