<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\PeopleGroup
 *
 * @deprecated feature is being removed
 * 
 * @property int $id
 * @property int $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property bool $is_institution_group
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\User[] $people
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup institutional()
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup personal($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup whereIsInstitutionGroup($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\PeopleGroup whereUserId($value)
 * @mixin \Eloquent
 */
class PeopleGroup extends Model
{
    /*
    id: bigIncrements
    user_id: User
    name: string
    created_at: date
    updated_at: date
    is_institution_group: boolean
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'peoplegroup';

    protected $fillable = ['user_id', 'name', 'is_institution_group'];

    public function user()
    {
        return $this->belongsTo(\KBox\User::class, 'user_id');
    }

    /**
     * [documents description]
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany [description]
     */
    public function people()
    {
        // Many to Many relation using the pivot table
        return $this->belongsToMany(\KBox\User::class, 'peoplegroup_to_user', 'peoplegroup_id', 'user_id');
    }
    
    public function scopeInstitutional($query)
    {
        return $query->where('is_institution_group', true);
    }
   
    public function scopePersonal($query, $user_id)
    {
        return $query->where('is_institution_group', false)->where('user_id', $user_id);
    }
//
//    public function scopeByDocumentId($query, $document_id)
//    {
//        return $query->where('document_id', $document_id);
//    }
//
//
//    public static function existsByDocumentAndUserId($document_id, $user_id)
//    {
//        return !is_null(self::ofUser($user_id)->byDocumentId($document_id)->first());
//    }
//
//    public static function getByDocumentAndUserId($document_id, $user_id)
//    {
//        return self::ofUser($user_id)->byDocumentId($document_id)->first();
//    }
}
