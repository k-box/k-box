<?php

namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use KlinkDMS\Traits\LocalizableDateFields;
use Dyrynda\Database\Support\GeneratesUuid;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\KlinkDocumentDescriptor;

/**
 * A Document Descriptor
 *
 * @property int $id the autoincrement identifier of the descriptor
 * @property uuid $uuid the UUID used to identify the document
 * @property int $institution_id the institution reference identifier of the User at the time of the descriptor creation
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
 * @property-read \KlinkDMS\File $file The last version of the document, described by this document descriptor
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\KlinkDMS\Group[] $groups the collections in which this document is categorized
 * @property-read \KlinkDMS\Institution $institution the institution to which this document pertain
 * @property-read \KlinkDMS\User $owner the user that is owning the document in the K-Box
 * @property-read \Illuminate\Database\Eloquent\Collection|\KlinkDMS\Shared[] $shares the shares of the document
 * @property-read \Illuminate\Database\Eloquent\Collection|\KlinkDMS\Starred[] $stars the star applied to this document by the users
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor fromKlinkID($institution_id, $document_id) {@see KlinkDMS\DocumentDescriptor::scopeFromKlinkID()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor fromOwnerId($owner_id) {@see \KlinkDMS\DocumentDescriptor::scopeFromOwnerId()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor fromUser($user_id) {@see \KlinkDMS\DocumentDescriptor::scopeFromUser()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor indexed() {@see \KlinkDMS\DocumentDescriptor::scopeIndexed()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor local() {@see \KlinkDMS\DocumentDescriptor::scopeLocal()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor notIndexed() {@see \KlinkDMS\DocumentDescriptor::scopeNotIndexed()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor ofUser($user_id) {@see \KlinkDMS\DocumentDescriptor::scopeOfUser()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor pending() {@see \KlinkDMS\DocumentDescriptor::scopePending()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor private() {@see \KlinkDMS\DocumentDescriptor::scopePrivate()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor public() {@see \KlinkDMS\DocumentDescriptor::scopePublic()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereAbstract($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereAuthors($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereDocumentType($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereDocumentUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereHash($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereInstitutionId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereIsPublic($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereLanguage($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereLastError($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereLocalDocumentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereMimeType($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereOwnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereThumbnailUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereUserOwner($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereUserUploader($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor whereVisibility($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor withIndexingError() {@see \KlinkDMS\DocumentDescriptor::withIndexingError()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\DocumentDescriptor withVisibility($visibility) {@see \KlinkDMS\DocumentDescriptor::withVisibility()}
 * @mixin \Eloquent
 */
class DocumentDescriptor extends Model
{
    use SoftDeletes, LocalizableDateFields, GeneratesUuid;

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

    protected $fillable = ['owner_id','institution_id', 'file_id','local_document_id','title','hash','document_uri','thumbnail_uri','mime_type','visibility','document_type','user_owner','user_uploader','abstract','language','authors'];
    
    protected $casts = [
        // Cast the UUID field to UUID, which is a binary field
        // instead of a varchar, see https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/
        // and https://github.com/michaeldyrynda/laravel-efficient-uuid
        'uuid' => 'uuid'
    ];
    
    protected $hidden = [ 'last_error' ];

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
        return $this->hasOne('\KlinkDMS\File', 'id', 'file_id');
    }

    public function stars()
    {
        return $this->hasMany('\KlinkDMS\Starred', 'document_id');
    }

    public function institution()
    {
        return $this->belongsTo('\KlinkDMS\Institution');
    }

    public function groups()
    {
        return $this->belongsToMany('\KlinkDMS\Group', 'document_groups', 'document_id');
    }

    public function shares()
    {
        return $this->morphMany('KlinkDMS\Shared', 'shareable');
    }

    public function owner()
    {
        return $this->belongsTo('KlinkDMS\User', 'owner_id', 'id');
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
        return $this->shares()->where('sharedwith_type', 'KlinkDMS\PublicLink')->count() > 0;
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

    /**
     * Select only the DocumentDescriptor that are physically local to the DMS
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocal($query)
    {
        return $query->where('file_id', '!=', 'null');
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
     * Scope queries to find by empty UUID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithNullUuid($query)
    {
        return $query->withTrashed()
                    //  ->whereUuid("00000000-0000-0000-0000-000000000000")
                     ->where('uuid', 0);
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
     * Filter by institution identifier and local document id.
     *
     * @param  integer $institution_id the ID of the institution as expressed by the id field of a cached Institution
     * @param  string $document_id    [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromKlinkID($query, $institution_id, $document_id)
    {
        return $query->where('institution_id', $institution_id)->where('local_document_id', $document_id);
    }
    
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
     * Get the institution that correspond to the given Klink Institution Identifier
     * @param  string $id [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function findByInstitutionAndDocumentId($institution_id, $id)
    {
        return self::fromKlinkID($institution_id, $id)->first();
    }
    
    public static function findByDocumentId($localDocumentId)
    {
        return self::fromLocalDocumentId($localDocumentId)->first();
    }

    /**
     * Check if a document descriptor exists in the local cache given it's institution id and K-Link local document id
     * @param  int $institution_id    [description]
     * @param  string $local_document_id The K-Link Local document id
     * @return boolean                    [description]
     */
    public static function existsByInstitutionAndDocumentId($institution_id, $local_document_id)
    {
        return ! is_null(self::findByInstitutionAndDocumentId($institution_id, $local_document_id));
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

    public function isPublic()
    {
        return $this->visibility === KlinkVisibilityType::KLINK_PUBLIC || $this->is_public;
    }

    public function isPrivate()
    {
        return $this->visibility === KlinkVisibilityType::KLINK_PRIVATE;
    }

    public function isPublicAndPrivate()
    {
        return $this->visibility === KlinkVisibilityType::KLINK_PRIVATE && $this->is_public;
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
            $need_public ? KlinkVisibilityType::KLINK_PUBLIC : KlinkVisibilityType::KLINK_PRIVATE);

        if (! $need_public && ! $this->groups->isEmpty()) {

            // the document is in a project only if the root collection
            // of the project (or one of its children) is attached to the document

            $each = $this->groups->map(function ($el) {
                return $el->toKlinkGroup();
            });

            $projects = $this->projects()->map(function ($el) {
                return ! is_null($el) && is_a($el, 'KlinkDMS\Project') ? 'p'.$el->id : null;
            });
            
            // $descr->setProjects(array_filter($projects->toArray()));
            $descr->setCollections(array_filter($each->merge($projects)->toArray()));
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
        // $local_inst = Institution::findByKlinkID($instance->getInstitutionID());

        // $cached = self::create([
        //     'institution_id' => $local_inst->id,
        //     'local_document_id' => $instance->getLocalDocumentID(),
        //     'title' => $instance->getTitle(),
        //     'hash' => $instance->getHash(),
        //     'created_at' => Carbon::createFromFormat(\DateTime::RFC3339, $instance->creationDate),
        //     'document_uri' => $instance->getDocumentUri(),
        //     'thumbnail_uri' => $instance->getThumbnailURI(),
        //     'mime_type' => $instance->getMimeType(),
        //     'visibility' => $instance->getVisibility(),
        //     'document_type' => $instance->getDocumentType(),
        //     'user_owner' => $instance->getUserOwner(),
        //     'user_uploader' => $instance->getUserUploader(),
        //     'abstract' => $instance->getAbstract(),
        //     'language' => $instance->getLanguage(),
        //     'is_public' => $instance->getVisibility() == KlinkVisibilityType::KLINK_PUBLIC,
        //     'authors' => is_array($instance->getAuthors()) ? implode(',', $instance->getAuthors()) : $instance->getAuthors(), //is Array so there is something that might be done
        // ]);
        
        // return $cached;
    }

    public function getIsPublicAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function isMine()
    {
        return $this->file_id != null;
    }

    public function isRemoteWebPage()
    {
        return ($this->document_type == 'web-page' || $this->document_type == 'document') && ! is_null($this->file) && starts_with($this->file->original_uri, 'http');
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

        return $this->file->upload_completed;
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
