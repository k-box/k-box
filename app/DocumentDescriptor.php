<?php

namespace KBox;

use Markdown;
use KBox\Traits\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KBox\Traits\LocalizableDateFields;
use Dyrynda\Database\Support\GeneratesUuid;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use KBox\Documents\Services\DocumentsService;
use OneOffTech\Licenses\Contracts\LicenseRepository;
use KBox\Events\DocumentDescriptorDeleted;
use KBox\Events\DocumentDescriptorRestored;
use KBox\Traits\ScopeNullUuid;
use Illuminate\Support\Str;

/**
 * A Document Descriptor
 *
 * @property int $id the autoincrement identifier of the descriptor
 * @property \Ramsey\Uuid\Uuid $uuid the UUID used to identify the document
 * @property string $local_document_id the K-Link Local Document Identifier
 * @property string $hash the SHA-512 hash of the last version of the underlying File
 * @property string $title the title
 * @property string $document_uri the URL at which this descriptor can be reached
 * @property string $thumbnail_uri the URL at which the thumbnail of the File's latest version can be reached
 * @property string $mime_type the mime type of the underlying File
 * @property string $visibility the visibility
 * @property string $document_type the document type according to the K-Link conversion table
 * @property string $user_owner The user that created the descriptor, in the format `User <email>`
 * @property string $user_uploader The user that uploaded the file attached to this descriptor, in the format `User <email>`
 * @property string $abstract the abstract of the document
 * @property string $language the language of the document
 * @property string $authors the authors of the document
 * @property \Carbon\Carbon $deleted_at when the document was trashed
 * @property \Carbon\Carbon $created_at when the document was created
 * @property \Carbon\Carbon $updated_at when the document was last updated
 * @property \Carbon\Carbon $failed_at when the last error occurred
 * @property int $file_id the reference to the File
 * @property int $owner_id the reference to the User that created the descriptor
 * @property int $status the status of the descriptor in the K-Box
 * @property bool $is_public
 * @property string $last_error
 * @property-read \KBox\File $file The last version of the document, described by this document descriptor
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\KBox\Group[] $groups the collections in which this document is categorized
 * @property-read \KBox\User $owner the user that is owning the document in the K-Box
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Shared[] $shares the shares of the document
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Starred[] $stars the star applied to this document by the users
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor fromKlinkID($institution_id, $document_id) {@see KBox\DocumentDescriptor::scopeFromKlinkID()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor fromOwnerId($owner_id) {@see \KBox\DocumentDescriptor::scopeFromOwnerId()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor fromUser($user_id) {@see \KBox\DocumentDescriptor::scopeFromUser()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor indexed() {@see \KBox\DocumentDescriptor::scopeIndexed()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor local() {@see \KBox\DocumentDescriptor::scopeLocal()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor notIndexed() {@see \KBox\DocumentDescriptor::scopeNotIndexed()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor ofUser($user_id) {@see \KBox\DocumentDescriptor::scopeOfUser()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor pending() {@see \KBox\DocumentDescriptor::scopePending()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor private() {@see \KBox\DocumentDescriptor::scopePrivate()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor public() {@see \KBox\DocumentDescriptor::scopePublic()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereAbstract($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereAuthors($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereDocumentType($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereDocumentUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereHash($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereIsPublic($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereLanguage($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereLastError($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereLocalDocumentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereMimeType($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereOwnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereThumbnailUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereUserOwner($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereUserUploader($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor whereVisibility($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor withIndexingError() {@see \KBox\DocumentDescriptor::withIndexingError()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\DocumentDescriptor withVisibility($visibility) {@see \KBox\DocumentDescriptor::withVisibility()}
 * @mixin \Eloquent
 */
class DocumentDescriptor extends Model
{
    use SoftDeletes, LocalizableDateFields, GeneratesUuid, Publishable, ScopeNullUuid;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, has not yet been indexed
     *
     * @var int
     * @deprecated
     */
    const STATUS_NOT_INDEXED = 0;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, is currently in indexing
     * @deprecated see self::STATUS_PROCESSING
     */
    const STATUS_PENDING = 1;

    /**
     * Indicate that the document is being processed (indexing, metadata extraction,...)
     */
    const STATUS_PROCESSING = 1;
    
    /**
     * Indicate that the document is being uploaded
     */
    const STATUS_UPLOADING = 5;

    /**
     * Indicate that the document upload is complete
     */
    const STATUS_UPLOAD_COMPLETED = 6;

    /**
     * Indicate that the document upload was cancelled
     */
    const STATUS_UPLOAD_CANCELLED = 7;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, has been indexed
     * @deprecated see self::STATUS_COMPLETED
     */
    const STATUS_INDEXED = 2;
    
    /**
     * Indicate that all the processing to the DocumentDescriptor was done succesfully
     */
    const STATUS_COMPLETED = 2;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, cannot be indexed due to one or more errors
     */
    const STATUS_ERROR = 3;

    /**
     * Removing from index
     */
    const STATUS_REMOVING = 4;

    /**
     * Define the content of the visibiliy attribute if the document is visible in both public and private searches
     */
    const VISIBILITY_ALL = 'all';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'document_descriptors';

    protected $dates = ['deleted_at'];

    protected $fillable = ['owner_id','file_id','local_document_id','title','hash','document_uri','thumbnail_uri','mime_type','visibility','document_type','user_owner','user_uploader','abstract','language','authors'];
    
    protected $casts = [
        // Cast the UUID field to UUID, which is a binary field
        // instead of a varchar, see https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/
        // and https://github.com/michaeldyrynda/laravel-efficient-uuid
        'uuid' => 'uuid',
        'copyright_owner' => 'collection',
    ];
    
    protected $hidden = [ 'last_error' ];
    protected $append = [ 'publication' ];

    protected $dispatchesEvents = [
        'deleted' => DocumentDescriptorDeleted::class,
        'restored' => DocumentDescriptorRestored::class,
    ];

    /**
     * Return the name of the pivot table that handles the relation
     * document descriptors => groups
     */
    public function getDocumentGroupsPivotTable()
    {
        return 'document_groups';
    }

    /**
     * The File that belongs to this document descriptor
     */
    public function file()
    {
        return $this->hasOne(\KBox\File::class, 'id', 'file_id')->withTrashed();
    }

    public function stars()
    {
        return $this->hasMany(\KBox\Starred::class, 'document_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'document_groups', 'document_id')
            ->using(DocumentGroups::class)
            ->withTimestamps()
            ->withPivot('added_by');
    }

    public function shares()
    {
        return $this->morphMany(\KBox\Shared::class, 'shareable');
    }

    public function owner()
    {
        return $this->belongsTo(\KBox\User::class, 'owner_id', 'id')->withTrashed();
    }

    /**
     * All duplicates of the document in the system
     */
    public function duplicates()
    {
        return $this->hasMany(\KBox\DuplicateDocument::class, 'duplicate_document_id', 'id');
    }

    /**
     * Get the Projects that contain this document descriptor
     *
     * @return Collection return a {@see Collection} of {@see Project}
     */
    public function projects()
    {
        $projects = $this->groups()->public()->with('project')->get();

        $projects = $projects->map(function ($el) {
            if (! $el->project) {
                $root = $el->getAncestorsWhere('parent_id', '=', null)->first();

                return ! is_null($root) ? $root->project : false;
            }

            return $el->project;
        })->filter();

        return $projects;
    }

    public function isShared()
    {
        return $this->shares()->count() > 0;
    }
    
    /**
     * Check if the document is accessible via a public link
     *
     * @return bool
     */
    public function hasPublicLink()
    {
        return $this->shares()->where('sharedwith_type', \KBox\PublicLink::class)->count() > 0;
    }
    
    public function isStarred($user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = $this->owner_id;
        }

        return $this->stars()->ofUser($user_id)->count() > 0;
    }

    public function getStar($user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = $this->onwer_id;
        }

        return $this->stars()->ofUser($user_id)->first();
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Select only the DocumentDescriptor that are physically local to the K-Box
     *
     * Will not select
     * - document descriptors that don't have a file
     * - document descriptor whose file upload was cancelled
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocal($query)
    {
        return $query->where([
            ['file_id', '!=', 'null'],
            ['status', '!=', self::STATUS_UPLOAD_CANCELLED],
            ['status', '!=', self::STATUS_UPLOADING],
        ]);
    }

    /**
     * Filters the Document description based on the document visibility.
     *
     * The filter will return also the document that has both public and private visibility
     *
     *
     * @param  string $visibility The visibility of the document to retrieve. {@see \Klink\DmsAdapter\KlinkVisibilityType}
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithVisibility($query, $visibility)
    {
        return $query->whereVisibility($visibility);
    }

    public function scopePublic($query)
    {
        return $query->whereIsPublic(true);
    }

    public function scopePrivate($query)
    {
        return $query->whereVisibility(KlinkVisibilityType::KLINK_PRIVATE);
    }

    /**
     * Select only the documents that has a status of {@see DocumentDescriptor::STATUS_INDEXED}
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIndexed($query)
    {
        return $query->where('status', self::STATUS_INDEXED);
    }
    
    /**
     * Select the documents that have the status {@see DocumentDescriptor::STATUS_NOT_INDEXED} or {@see DocumentDescriptor::STATUS_ERROR}
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotIndexed($query)
    {
        return $query->where('status', self::STATUS_NOT_INDEXED)->orWhere('status', self::STATUS_ERROR);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeWithIndexingError($query)
    {
        return $query->where('status', self::STATUS_ERROR);
    }
    
    /**
     * Scope by the old document identifier
     *
     * @internal kept for compatibility reasons with old url format
     */
    public function scopeFromLocalDocumentId($query, $document_id)
    {
        return $query->where('local_document_id', $document_id);
    }
    
    public function scopeFromUser($query, $user_id)
    {
        return $query->where('owner_id', $user_id);
    }

    public function scopeOfUser($query, $user_id)
    {
        return $query->where('owner_id', $user_id);
    }

    /**
     * Filter the DocumentDescriptor by owner
     *
     *
     * @param  string $owner_id [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromOwnerId($query, $owner_id)
    {
        return $query->where('owner_id', $owner_id);
    }

    /**
     * Get the documents that correspond to the given owner
     * @param  string|integer $id [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function findByOwnerId($owner_id)
    {
        return self::fromOwnerId($owner_id)->get();
    }

    /**
     * Check if a descriptor exists given the hash. The descriptor is considered "existing" also if has been
     * soft deleted.
     *
     * The hash is considered unique in the table, otherwise the first descriptor with the
     * same hash will be considered
     *
     * @param  string $hash The hash
     * @return true if a descriptor exists, false otherwise.
     */
    public static function existsByHash($hash)
    {
        return ! is_null(self::withTrashed()->where('hash', $hash)->first());
    }

    /**
     * Search for a DocumentDescriptor based on the Hash
     *
     * @param  string $hash The hash
     * @return DocumentDescriptor the document descriptor instance if found
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException if the document descriptor cannot be found
     */
    public static function findByHash($hash)
    {
        $model = self::withTrashed()->where('hash', $hash)->firstOrFail();

        return $model;
    }

    // --- setten and getter for Visibility

    public function getIsPublicAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function isPublic()
    {
        return $this->is_public && $this->isPublished();
    }

    public function isPrivate()
    {
        return $this->visibility === KlinkVisibilityType::KLINK_PRIVATE;
    }

    public function isPublicAndPrivate()
    {
        return $this->visibility === KlinkVisibilityType::KLINK_PRIVATE && $this->is_public;
    }

    public function getCopyrightUsageAttribute($value = null)
    {
        if (! $value) {
            return Option::copyright_default_license();
        }

        $licenses = app()->make(LicenseRepository::class);
        
        return $licenses->find($value);
    }
    
    public function getCopyrightOwnerAttribute($value = null)
    {
        if (! $value) {
            return collect();
        }
        return $this->castAttribute('copyright_owner', $value);
    }

    public function getDocumentUriAttribute($value = null)
    {
        if ($this->file_id) {
            return route('documents.preview', ['uuid' => $this->uuid]);
        }

        return $value;
    }

    public function getThumbnailUriAttribute($value = null)
    {
        if ($this->file_id) {
            return route('documents.thumbnail', ['uuid' => $this->uuid]);
        }

        return $value;
    }

    public function getAbstractHtmlAttribute($value = null)
    {
        if (empty($this->abstract)) {
            return '';
        }

        return Markdown::convertToHtml($this->abstract);
    }

    // --- convert to/from KlinkDocumentDescriptor

    /**
     * Convert the current instance to a valid KlinkDocumentDescriptor
     * @param boolean $need_public if set to true the returned KlinkDocumentDescriptor will be for public indexing
     * @return KlinkDocumentDescriptor the conversion to the Klink Document Descriptor
     * @internal
     */
    public function toKlinkDocumentDescriptor($need_public = false)
    {
        $descr = KlinkDocumentDescriptor::make(
            $this,
            $need_public ? KlinkVisibilityType::KLINK_PUBLIC : KlinkVisibilityType::KLINK_PRIVATE
        );

        if (! $need_public && ! $this->groups->isEmpty()) {
            // the document is in a project only if the root collection
            // of the project (or one of its children) is attached to the document

            $collections = $this->groups->map(function ($el) {
                return $el->toKlinkGroup();
            });

            $projects = $this->projects()->map(function ($el) {
                return ! is_null($el) && is_a($el, \KBox\Project::class) ? $el->id : null;
            });
            
            $descr->setProjects(array_filter($projects->toArray()));
            $descr->setCollections(array_filter($collections->toArray()));
        }
        
        return $descr;
    }

    /**
     * Transform a remote KlinkDocumentDescriptor into a local cached DocumentDescriptor
     * @param  KlinkDocumentDescriptor $instance the KlinkDocumentDescriptor to be imported
     * @return DocumentDescriptor the local cached DocumentDescriptor
     * @internal
     */
    public static function fromKlinkDocumentDescriptor(KlinkDocumentDescriptor $instance)
    {
        throw new \Exception("Currently not supported");
    }

    public function isMine()
    {
        return $this->file_id != null;
    }

    public function isRemoteWebPage()
    {
        return ($this->document_type == 'web-page' || $this->document_type == 'document') && ! is_null($this->file) && Str::startsWith($this->file->original_uri, 'http');
    }
    
    /**
     * Tells if the descriptor (and the file content) is searchable
     *
     * @uses isFileUploadComplete()
     * @return bool
     */
    public function isIndexed()
    {
        return $this->isFileUploadComplete() &&
               $this->status !== self::STATUS_NOT_INDEXED &&
               $this->status !== self::STATUS_PROCESSING &&
               $this->status !== self::STATUS_ERROR;
    }
    
    /**
     * Tell if the file was uploaded completely or if is still pending
     *
     * @uses isMine()
     * @return bool true if the file has been uploaded completely
     */
    public function isFileUploadComplete()
    {
        if (! $this->isMine()) {
            return false;
        }

        // if file is null means that is trashed, therefore was succesfully uploaded
        return $this->file ? $this->file->upload_completed : true;
    }

    /**
     * Get the file versions of this document. It includes the current file version
     *
     * @return Illuminate\Support\Collection|File[] the ordered file versions. An empty collection can be returned if the current file version is trashed or cannot be found anymore in the system
     */
    public function fileVersions()
    {
        if (is_null($this->file)) {
            return collect();
        }

        return collect([$this->file])->merge($this->file->versions());
    }

    /**
     * Check if the document descriptor can be viewed by a user
     *
     * @param \KBox\User $user
     * @return bool
     */
    public function isAccessibleBy($user)
    {
        if ($this->isPublished() || $this->hasPublicLink()) {
            return true;
        }

        if (is_null($user) || ! is_a($user, User::class)) {
            return false;
        }

        $collections = $this->groups;
        $is_in_collection = false;

        if (! is_null($collections) && ! $collections->isEmpty()) {
            $serv = app(DocumentsService::class);

            $filtered = $collections->filter(function ($c) use ($serv, $user) {
                return $serv->isCollectionAccessible($user, $c);
            });
            
            $is_in_collection = ! $filtered->isEmpty();
        }

        $is_shared = $this->shares()->sharedWithMe($user)->count() > 0 ?: false;

        $owner = ! is_null($this->owner) ? $this->owner->id === $user->id || $user->isContentManager() : (is_null($this->owner) ? true : false);

        if ($is_in_collection || $is_shared || $owner) {
            return true;
        }

        return false;
    }

    /**
     * Check if the document can be edited by a user
     *
     * @param \KBox\User $user
     * @return bool
     */
    public function isEditableBy($user)
    {
        if (is_null($user) || ! is_a($user, User::class)) {
            return false;
        }

        if (! $this->isAccessibleBy($user)) {
            return false;
        }

        $has_edit_capability = $user->can_capability(Capability::EDIT_DOCUMENT);

        if (! $has_edit_capability) {
            return false;
        }

        $collections = $this->groups;
        $is_in_collection = false;

        if (! is_null($collections) && ! $collections->isEmpty()) {
            $serv = app(DocumentsService::class);

            $filtered = $collections->filter(function ($c) use ($serv, $user) {
                return $serv->isCollectionAccessible($user, $c);
            });
            
            $is_in_collection = ! $filtered->isEmpty();
        }

        $is_shared = $this->shares()->sharedWithMe($user)->count() > 0 ?: false;

        $owner = ! is_null($this->owner) ? $this->owner->id === $user->id || $user->isContentManager() : (is_null($this->owner) ? true : false);

        if ($is_in_collection || $is_shared || $owner) {
            return true;
        }

        return false;
    }
    
    public function setLastErrorAttribute($value)
    {
        if (is_a($value, '\Exception')) {
            $obj = new \stdClass;
            $obj->message = $value->getMessage();
            $obj->type = get_class($value);
            $obj->payload = $value;
            
            $value = $obj;
        } elseif (is_object($value)) {
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = get_class($value);
            $value = $obj;
        } elseif (is_array($value)) {
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = 'array';
            $value = $obj;
        } elseif (is_string($value) || is_numeric($value)  || is_bool($value)) {
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = is_string($value) ? 'string' : (is_bool($value) ? 'boolean' : 'number');
            $value = $obj;
        } elseif (! is_null($value)) {
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = 'unknown';
            $value = $obj;
        }
        
        $this->attributes['last_error'] = is_null($value) ? null : json_encode($value);
    }
    
    /**
     * Attribute modifier for last_error
     * Transform the encoded last_error in the database in a plain PHP object
     *
     * The returned object could have the following properties:
     * - `type`: the type of the entity stored
     * - `payload`: the original object stored
     * - `message`: (optional) The exception message, if the `payload` was an Exception instance or sub-class

     * The `type` property that can assume the following values
     * - `string`: in case the payload is a string
     * - `boolean`: in case the payload is a boolean
     * - `number`: in case the payload is a number
     * - `array`: in case the payload is an array
     * - class name: in case the payload is an object or an exception
     *
     */
    public function getLastErrorAttribute($value)
    {
        return is_null($value) ? null : json_decode($value);
    }
}
