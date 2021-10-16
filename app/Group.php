<?php

namespace KBox;

use KBox\Traits\ScopeNullUuid;
use KBox\Traits\LocalizableDateFields;
use Franzose\ClosureTable\Models\Entity;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use KBox\Events\CollectionCreated;
use KBox\Events\CollectionTrashed;
use KBox\Casts\UuidCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @property int $type
 * @property int $parent_id
 * @property int $position
 * @property int $real_depth
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\DocumentDescriptor[] $documents
 * @property-read \KBox\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Shared[] $shares
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder byName($name)
 * @method static \Illuminate\Database\Query\Builder roots()
 * @method static \Illuminate\Database\Query\Builder whereColor($value)
 * @method static \Illuminate\Database\Query\Builder whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder whereId($value)
 * @method static \Illuminate\Database\Query\Builder whereIsPrivate($value)
 * @method static \Illuminate\Database\Query\Builder whereName($value)
 * @method static \Illuminate\Database\Query\Builder whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder whereRealDepth($value)
 * @method static \Illuminate\Database\Query\Builder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder withAllDescendants()
 * @method static \Illuminate\Database\Query\Builder personalCollections($user)
 * @method static \Illuminate\Database\Query\Builder type($type)
 * @method static \Illuminate\Database\Query\Builder projectCollections()
 * @mixin \Eloquent
 */
class Group extends Entity
{

    /**
     * Personal collection type. A personal collection of a user.
     */
    public const TYPE_PERSONAL = 1;

    /**
     * Project collection type. A collection under a project.
     */
    public const TYPE_PROJECT = 2;

    // https://github.com/franzose/ClosureTable

    use SoftDeletes, LocalizableDateFields, GeneratesUuid, ScopeNullUuid;
    use HasFactory;

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

    protected $fillable = ['name','color', 'user_id','parent_id', 'type', 'is_private'];

    public $timestamps = true;

    protected $casts = [
        'uuid' => UuidCast::class,
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

    /**
     * Filter the collections of the specified type
     *
     * @param int $type the collection type
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeType($query, $type)
    {
        return $query->whereType($type);
    }

    public function scopeByName($query, $name)
    {
        return $query->whereName($name);
    }

    /**
     * Filter only personal collections of a specific user
     *
     * @param int $user_id
     */
    public function scopePersonalCollections($query, $user_id)
    {
        return $query->where(function ($query) use ($user_id) {
            $query->type(self::TYPE_PERSONAL)
                  ->where('user_id', $user_id);
        });
    }

    /**
     * Filter only project collections
     *
     * Equal to {@see type} with Group::TYPE_PROJECT as value
     */
    public function scopeProjectCollections($query)
    {
        return $query->type(self::TYPE_PROJECT);
    }

    /**
     * Filter root collections (i.e. without parents)
     */
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
            ->personalCollections($user_id)
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
            ->projectCollections()
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
        if ($value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        return $this->type === self::TYPE_PERSONAL;
    }

    public function getIsProjectCollectionAttribute($value)
    {
        return $this->type === self::TYPE_PROJECT;
    }

    public function getIsPersonalCollectionAttribute($value)
    {
        return $this->type === self::TYPE_PERSONAL;
    }

    /**
     * Transform the current collection into a project collection
     *
     * @return self
     */
    public function transformToProjectCollection()
    {
        $this->is_private = false;
        $this->type = Group::TYPE_PROJECT;
        $this->color = 'f1c40f';
        $this->save();

        return $this;
    }

    /**
     * Transform the current project collection into a personal collection
     *
     * @return self
     */
    public function transformToPersonalCollection()
    {
        $this->is_private = true;
        $this->type = Group::TYPE_PERSONAL;
        $this->color = '16a085';
        $this->save();

        return $this;
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

            $alreadyTrashed = $parent->getTrashedChildren()->where('name', $this->name)->where('type', $this->type)->first();
            
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
            $alreadyExisting = $parent->children(['*'])->where('name', $this->name)->type($this->type)->first();
            
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
            $prefix = $group->type === self::TYPE_PERSONAL ? 'dms_personal_collections' : 'dms_project_collections-';

            Cache::forget("{$prefix}{$group->user_id}");
            
            return $group;
        });
        
        static::updated(function ($group) {
            $prefix = $group->type === self::TYPE_PERSONAL ? 'dms_personal_collections' : 'dms_project_collections-';

            Cache::forget("{$prefix}{$group->user_id}");
            
            return $group;
        });
    }
}
