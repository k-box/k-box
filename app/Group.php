<?php
namespace KlinkDMS;

use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Entity implements GroupInterface
{

    // https://github.com/franzose/ClosureTable

    /*
    id: bigIncrements
    name: string
    color: string (hex color)
    created_at: date
    updated_at: date
    deleted_at: date
    user_id: User
    group_type_id: GroupType
    parent_id: Group
    is_private:boolean (default true)
    */

    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * ClosureTable model instance.
     *
     * @var groupClosure
     */
    protected $closure = 'KlinkDMS\GroupClosure';

    

    protected $fillable = ['name','color', 'user_id','parent_id', 'group_type_id', 'is_private'];

    public $timestamps = true;


    public function user(){
        
        // One to One
        return $this->belongsTo('KlinkDMS\User');

    }
    
    public function project(){

        return $this->belongsTo('KlinkDMS\Project', 'id', 'collection_id');
    }

    /**
     * [documents description]
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany [description]
     */
    public function documents()
    {
        return $this->belongsToMany('KlinkDMS\DocumentDescriptor', 'document_groups', 'group_id', 'document_id');
    }

    public function shares()
    {
        return $this->morphMany('KlinkDMS\Shared', 'shareable');
    }


    public function scopeOfType($query, $type)
    {
        return $query->whereType($type);
    }


    public function scopeByName($query, $name)
    {
        return $query->whereName($name);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }
    
    public function scopeOrPublic($query)
    {
        return $query->orWhere('is_private', false);
    }

    public function scopePrivate($query, $user_id)
    {
        return $query->where(function($query) use($user_id)
            {
                $query->where('is_private', true)
                      ->where('user_id', $user_id);
            });
    }
    
    public function scopeOrPrivate($query, $user_id)
    {

        return $query->orWhere(function($query) use($user_id)
            {
                $query->where('is_private', true)
                      ->where('user_id', $user_id);
            });
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    
    public static function getPersonalTree($user_id, array $columns = ['*'])
    {
        /**
         * @var Entity $instance
         */
        $instance = new static;
        $columns = $instance->prepareTreeQueryColumns($columns);

        

        return $instance
            ->where('is_private', '=', true)
            ->where('user_id', '=', $user_id)
            ->get($columns)->toTree();
    }



    /**
     * Convert the group to the Klink representation used in the KlinkDocumentDescriptor
     *
     * @return string|boolean the id of the group in the form user_id:group_id, false if is trashed
     */
    public function toKlinkGroup()
    {
        if($this->trashed()){
            return false;
        }
        $uid = $this->is_private ? $this->user_id : 0 ;

        return $uid . ':' . $this->id;
    }


    public function getCreatedAt(){
        return $this->created_at->format(trans('units.date_format'));
    }
    
    /**
     * Overcome the problem that is_private is stored as a string because it is in a key
     */
    public function getIsPrivateAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    
    public static function getClosureTable(){
        $instance = new static;
        return $instance->closure;
    }
    
    static function boot()
    {
        parent::boot();

        static::created(function ($group)
        {
            if($group->is_private){
                \Cache::forget('dms_personal_collections'. $group->user_id);
            }
            else {
                \Cache::forget('dms_project_collections-' . $group->user_id);
            }
            
            return $group;

        });
        
        static::updated(function ($group)
        {
            if($group->is_private){
                \Cache::forget('dms_personal_collections'. $group->user_id);
            }
            else {
                \Cache::forget('dms_project_collections-' . $group->user_id);
            }
            
            return $group;

        });
    }
}
