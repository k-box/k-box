<?php

namespace KBox;

use KBox\Traits\ScopeNullUuid;
use KBox\Traits\LocalizableDateFields;
use Franzose\ClosureTable\Models\Entity;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use KBox\Events\CollectionCreated;
use KBox\Events\CollectionTrashed;
use Dyrynda\Database\Casts\EfficientUuid;

/**
 * A collection of document descriptors
 *
 * @property int $id
 * @property \Ramsey\Uuid\Uuid $uuid the UUID that identify the group/collection
 * @property int $user_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $color
 * @property string $is_private
 * @property int $group_type_id
 * @property int $parent_id
 * @property int $position
 * @property int $real_depth
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\DocumentDescriptor[] $documents
 * @property-read \KBox\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Shared[] $shares
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group byName($name)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group ofType($type)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group orPrivate($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group orPublic()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group private($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group public()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group roots()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereColor($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereGroupTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereIsPrivate($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereRealDepth($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group withAllDescendants()
 * @mixin \Eloquent
 */
class Group extends Entity
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

    use SoftDeletes, LocalizableDateFields, GeneratesUuid, ScopeNullUuid;

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
    protected $closure = GroupClosure::class;

    protected $fillable = ['name','color', 'user_id','parent_id', 'group_type_id', 'is_private'];

    public $timestamps = true;

    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    protected $dispatchesEvents = [
        'created' => CollectionCreated::class,
        'deleted' => CollectionTrashed::class,
    ];

    public function user()
    {
        
        // One to One
        return $this->belongsTo(\KBox\User::class)->withTrashed();
    }
    
    public function project()
    {
        return $this->belongsTo(\KBox\Project::class, 'id', 'collection_id');
    }

    /**
     * Get the project that contains this collection.
     *
     * @return KBox\Project|null the project that contains the collection, or null if not in project or personal
     */
    public function getProject()
    {
        if ($this->is_private) {
            return null;
        }

        if (! is_null($this->project)) {
            return $this->project;
        }

        $project_root = $this->withAncestors()->has('project')->with('project')->first();

        if (! is_null($project_root)) {
            return $project_root->project;
        }

        return null;
    }

    /**
     * [documents description]
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany [description]
     */
    public function documents()
    {
        return $this->belongsToMany(DocumentDescriptor::class, 'document_groups', 'group_id', 'document_id')
            ->using(DocumentGroups::class)
            ->withTimestamps()
            ->withPivot('added_by')
            ->local();
    }

    /**
     * Get this group plus all descendants query
     */
    public function scopeWithAllDescendants($query)
    {
        return $this->scopeDescendantsWithSelf($query);
    }
    
    public function scopeWithDescendants($query)
    {
        return $this->scopeDescendants($query);
    }
    
    public function scopeWithAncestors($query)
    {
        return $this->scopeAncestors($query);
    }

    public function shares()
    {
        return $this->morphMany(\KBox\Shared::class, 'shareable');
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
        return $query->where(function ($query) use ($user_id) {
            $query->where('is_private', true)
                      ->where('user_id', $user_id);
        });
    }
    
    public function scopeOrPrivate($query, $user_id)
    {
        return $query->orWhere(function ($query) use ($user_id) {
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
            ->orderBy('name', 'asc')
            ->get($columns)->toTree();
    }

    public static function getProjectsTree(array $columns = ['*'])
    {
        /**
         * @var Entity $instance
         */
        $instance = new static;
        $columns = $instance->prepareTreeQueryColumns($columns);

        return $instance
            ->where('is_private', '=', false)
            ->orderBy('name', 'asc')
            ->get($columns)->toTree();
    }

    /**
     * Convert the group to the K-Search collection representation
     *
     * @return string|boolean the id of the group, false if is trashed
     */
    public function toKlinkGroup()
    {
        if ($this->trashed()) {
            return false;
        }

        return $this->id;
    }
    
    /**
     * Overcome the problem that is_private is stored as a string because it is in a key
     */
    public function getIsPrivateAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    public function isSharedWith($user)
    {
        return $this->shares()->sharedWithMe($user)->count() > 0;
    }

    public function getNameAttribute($value)
    {

        // some values can be escaped, like the single quote char ' to #039; and needs to be escaped
        return htmlspecialchars_decode($value, ENT_QUOTES);
    }
    
    public static function getClosureTable()
    {
        $instance = new static;
        return $instance->closure;
    }

    public function getTrashedChildren()
    {
        return $this->children(['*'])->onlyTrashed()->get();
    }
    
    public function hasTrashedChildren()
    {
        return $this->children(['*'])->onlyTrashed()->count() > 0;
    }

    /**
     * Merge the specified $collection into the current instance.
     *
     * Move the children (including trashed ones) of $collection under the current instance
     *
     * @param Group $collection the collection to be merged
     * @return Group
     */
    public function merge(Group $collection)
    {
        if ($collection->children(['*'])->withTrashed()->count() === 0) {
            // not using the shortcut hasChildren because
            // does not take into account the trashed collections
            return $this;
        }

        $collection->children(['*'])->withTrashed()->each(function ($child) {
            $child->moveTo(0, $this);
        });

        return $this;
    }

    /**
     * Trash the collection
     */
    public function trash()
    {
        $parent = static::withTrashed()->find($this->parent_id); //getParent();

        if ($parent && $parent->hasTrashedChildren()) {
            // if the parent has trashed children and within those there is one with the same name
            // we need to merge the childrens and permanently trash this collection instead of
            // simply move it to the trash, as it will fail due to the uniqueness constraint

            $alreadyTrashed = $parent->getTrashedChildren()->where('name', $this->name)->where('is_private', $this->is_private)->first();
            
            if ($alreadyTrashed) {
                // what if $alreadyTrashed is personal, but of another user
                $isUserPrivate = $this->is_private && $alreadyTrashed->user_id === $this->user_id;
                $hasSameVisibility = $this->is_private === $alreadyTrashed->is_private;
                $canBeMerged = $hasSameVisibility || ($hasSameVisibility && $isUserPrivate);
                
                if ($canBeMerged) {
                    $this->merge($alreadyTrashed);

                    $this->getChildren()->each->trash();
        
                    $alreadyTrashed->forceDelete();
                    return $this->delete();
                }
            }
        }

        $this->getChildren()->each->trash();
        
        return $this->delete();
    }

    /**
     * Restore from trash
     *
     * @return Group the restored collection. It can be different if a collection exists in the original position
     */
    public function restoreFromTrash()
    {
        $parent = $this->getParent();
        if ($parent && $parent->hasChildren()) {
            // if the parent has children and within those there is one with the same name
            // we need to merge the childrens and permanently trash this collection instead of
            // restore it from the trash
            $alreadyExisting = $parent->children(['*'])->where('name', $this->name)->where('is_private', $this->is_private)->first();
            
            if ($alreadyExisting) {
                // what if $alreadyExisting is personal, but of another user
                $isUserPrivate = $this->is_private && $alreadyExisting->user_id === $this->user_id;
                $hasSameVisibility = $this->is_private === $alreadyExisting->is_private;
                $canBeMerged = $hasSameVisibility || ($hasSameVisibility && $isUserPrivate);
    
                if ($canBeMerged) {
                    $this->getTrashedChildren()->each->restoreFromTrash();
                    
                    $alreadyExisting->merge($this);
    
                    $this->forceDelete();
    
                    return $alreadyExisting->fresh();
                }
            }
        }
        
        $this->restore();
        
        $this->getTrashedChildren()->each->restoreFromTrash();
        
        return $this->fresh();
    }
    
    public static function boot()
    {
        parent::boot();

        static::created(function ($group) {
            if ($group->is_private) {
                \Cache::forget('dms_personal_collections'.$group->user_id);
            } else {
                \Cache::forget('dms_project_collections-'.$group->user_id);
            }
            
            return $group;
        });
        
        static::updated(function ($group) {
            if ($group->is_private) {
                \Cache::forget('dms_personal_collections'.$group->user_id);
            } else {
                \Cache::forget('dms_project_collections-'.$group->user_id);
            }
            
            return $group;
        });
    }
}
