<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KlinkDMS\Starred;
use Carbon\Carbon;

class DocumentDescriptor extends Model {

    /**
     * Indicate that the document, reference by a DocumentDescriptor, has not yet been indexed
     *
     * @var int
     */
    const STATUS_NOT_INDEXED = 0;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, is currently in indexing
     */
    const STATUS_PENDING = 1;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, has been indexed
     */
    const STATUS_INDEXED = 2;

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


    // const VISIBILITY_PUBLIC = 'public';

    // const VISIBILITY_PRIVATE = 'private';

    // const VISIBILITY_ALL = 'all';

    /*
    id: bigIncrements
    institution_id: Institution
    local_document_id: string
    title: string
    hash: string
    document_uri: string
    thumbnail_uri: string
    mime_type: string
    visibility: string
    document_type: string
    user_owner: string
    user_uploader: string
    abstract: string
    language: string
    authors: string (serialized)
    file_id: File
    owner_id: User
    created_at
    updated_at
    status
    is_public: boolean
    last_error: json 
    
    */

    use SoftDeletes;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'document_descriptors';

    protected $dates = ['deleted_at'];

    protected $fillable = ['owner_id','institution_id', 'file_id','local_document_id','title','hash','document_uri','thumbnail_uri','mime_type','visibility','document_type','user_owner','user_uploader','abstract','language','authors'];
    
    protected $casts = [
        // 'last_error' => 'array',
    ];
    
    protected $hidden = [ 'last_error' ];

    public function file(){
        
        // One to One
        return $this->belongsTo('\KlinkDMS\File');

    }


    public function stars(){
        
        return $this->hasMany('\KlinkDMS\Starred', 'document_id');

    }

    public function institution(){
        
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


    public function isShared()
    {
        return $this->shares()->count() > 0;
    }

    public function isStarred($user_id = null)
    {
        if(is_null($user_id)){
            $user_id = $this->owner_id;
        }

        return $this->stars()->ofUser($user_id)->count() > 0;
    }

    public function getStar($user_id = null)
    {
        if(is_null($user_id)){
            $user_id = $this->onwer_id;
        }

        return $this->stars()->ofUser($user_id)->first();
    }


    /**
     * Select only the DocumentDescriptor that are physically local to the DMS
     * @return [type] [description]
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
     * @param  [type] $query      [description]
     * @param  [type] $visibility [description]
     * @return [type]             [description]
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
        return $query->whereVisibility(\KlinkVisibilityType::KLINK_PRIVATE);
    }




    /**
     * Select only the documents that has a status of {@see DocumentDescriptor::STATUS_INDEXED}
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeIndexed($query)
    {
        return $query->where('status', self::STATUS_INDEXED);
    }
    
    /**
     * Select the documents that have the status {@see DocumentDescriptor::STATUS_NOT_INDEXED} or {@see DocumentDescriptor::STATUS_ERROR}
     * @param  [type] $query [description]
     * @return [type]        [description]
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
     * @param  [type] $query          [description]
     * @param  integer $institution_id the ID of the institution as expressed by the id field of a cached Institution
     * @param  string $document_id    [description]
     * @return [type]                 [description]
     */
    public function scopeFromKlinkID($query, $institution_id, $document_id)
    {
        return $query->where('institution_id', $institution_id)->where('local_document_id', $document_id);
    }
    
    public function scopeFromUser($query,$user_id)
    {
    	return $query->where('owner_id',$user_id);
    }

    public function scopeOfUser($query,$user_id)
    {
        return $query->where('owner_id',$user_id);
    }

    /**
     * Filter the DocumentDescriptor by owner
     * 
     * @param  [type] $query    [description]
     * @param  [type] $owner_id [description]
     * @return [type]           [description]
     */
    public function scopeFromOwnerId($query, $owner_id)
    {
        return $query->where('owner_id', $owner_id);
    }

    /**
     * Get the documents that correspond to the given owner
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function findByOwnerId($owner_id)
    {
        return self::fromOwnerId( $owner_id )->get();
    }

    /**
     * Get the institution that correspond to the given Klink Institution Identifier
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function findByInstitutionAndDocumentId($institution_id, $id)
    {
        return self::fromKlinkID( $institution_id, $id )->first();
    }

    /**
     * Check if a document descriptor exists in the local cache given it's institution id and K-Link local document id
     * @param  int $institution_id    [description]
     * @param  string $local_document_id The K-Link Local document id
     * @return boolean                    [description]
     */
    public static function existsByInstitutionAndDocumentId($institution_id, $local_document_id)
    {
        return !is_null(self::findByInstitutionAndDocumentId($institution_id, $local_document_id));
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
        return !is_null(self::withTrashed()->where('hash', $hash)->first());
    }


    // --- setten and getter for Visibility


    // public function setVisibilityAttribute($value)
    // {

    //     // se non avevo visibility, la setto
    //     // se era public e chiedo public no problem (idem per private)
    //     // 
    //     // se era private e chiedo public => both
    //     if(empty($this->attributes['visibility'])){
    //         //TODO: check if the $value is valid
    //         $this->attributes['visibility'] = $value;
    //     }
    //     else if(($this->attributes['visibility'] === \KlinkVisibilityType::KLINK_PUBLIC && $value === \KlinkVisibilityType::KLINK_PRIVATE) ||
    //             ($this->attributes['visibility'] === \KlinkVisibilityType::KLINK_PRIVATE && $value === \KlinkVisibilityType::KLINK_PUBLIC)){
    //         $this->attributes['visibility'] = self::VISIBILITY_ALL;
    //     }

        
        

         
    // }


    public function isPublic()
    {
        return $this->visibility === \KlinkVisibilityType::KLINK_PUBLIC || $this->is_public;
    }

    public function isPrivate()
    {
        return $this->visibility === \KlinkVisibilityType::KLINK_PRIVATE;
    }

    public function isPublicAndPrivate()
    {
        return $this->visibility === \KlinkVisibilityType::KLINK_PRIVATE && $this->is_public;
    }


    // --- convertion to/from KlinkDocumentDescriptor

    /**
     * Convert the current instance to a valid KlinkDocumentDescriptor
     * @return [type] [description]
     * @param boolean $need_public if set to true the returned KlinkDocumentDescriptor will be for public indexing
     * @internal
     */
    public function toKlinkDocumentDescriptor($need_public = false)
    {

        $inst = $this->institution;
        
        $institution = config('dms.institutionID'); //fallback if institution is null
        if(!is_null($inst)){
            $institution = $this->institution->klink_id;
        }

        $descr = \KlinkDocumentDescriptor::create(
            $institution,
            $this->local_document_id,
            $this->hash,
            $this->title,
            $this->mime_type,
            $this->document_uri,
            $this->thumbnail_uri,
            $this->user_uploader,
            $this->user_owner,
            $need_public ? \KlinkVisibilityType::KLINK_PUBLIC : $this->visibility,
            !is_null($this->created_at) ? $this->created_at->toRfc3339String() : \KlinkHelpers::now()
        );

        if(!is_null($this->abstract)){
            $descr->setAbstract($this->abstract);
        }

        if(!is_null($this->language) && $this->language !== 'unknown'){
            $descr->setLanguage($this->language);
        }

        if(!is_null($this->authors)){
            $descr->setAuthors( explode(',', $this->authors) );
        }
        
        if($this->isMine()){
            $names = array($this->file->name);
            
            $parts = preg_split( '/(\-|_|,|\.)/', $this->file->name );
            
            $names = array_merge($names, $parts);
            
            $descr->setTitleAliases(array_filter($names));
        }

        if(!$need_public && !$this->groups->isEmpty()){

            $each = $this->groups->map(function($el){
                return $el->toKlinkGroup();
            });

            $descr->setDocumentGroups( array_filter( $each->toArray() ) );

        }

        return $descr;
    }

    /**
     * Merge the current saved descriptor with the new information coming from a 
     * KlinkDocumentDescriptor
     * 
     * @param  \KlinkDocumentDescriptor $instance [description]
     * @return [type]                             [description]
     */
    public function mergeWithKlinkDocumentDescriptor( \KlinkDocumentDescriptor $instance ){

        if( $this->hash === $instance->getHash() && 
            // $this->institution_id === $instance->getInstitutionID() &&
            $this->local_document_id === $instance->getLocalDocumentID()){

            $this->abstract = $instance->getAbstract();
            $this->language = $instance->getLanguage();
            if(is_array($instance->getAuthors())){
                $this->authors = implode(',', $instance->getAuthors());
            }
            else {
                \Log::warning('Klink Document Descriptor merge, authors not an array', ['descriptor' => $instance, 'authors' => var_export($instance->getAuthors(), true)]);
            }

        }
        else {
            throw new \InvalidArgumentException("Trying to merge two descriptors of different documents", 108);
            
        }


        return $this;
    }

    /**
     * Transform a remote KlinkDocumentDescriptor into a local cached DocumentDescriptor
     * @param  \KlinkDocumentDescriptor $instance the KlinkDocumentDescriptor to be imported
     * @return DocumentDescriptor the local cached DocumentDescriptor
     * @internal
     */
    public static function fromKlinkDocumentDescriptor( \KlinkDocumentDescriptor $instance )
    {
    
        $local_inst = Institution::findByKlinkID($instance->getInstitutionID());

        $cached = self::create(array(
            'institution_id' => $local_inst->id,
            'local_document_id' => $instance->getLocalDocumentID(),
            'title' => $instance->getTitle(),
            'hash' => $instance->getHash(),
            'created_at' => Carbon::createFromFormat( \DateTime::RFC3339, $instance->creationDate ),
            'document_uri' => $instance->getDocumentUri(),
            'thumbnail_uri' => $instance->getThumbnailURI(),
            'mime_type' => $instance->getMimeType(),
            'visibility' => $instance->getVisibility(),
            'document_type' => $instance->getDocumentType(),
            'user_owner' => $instance->getUserOwner(),
            'user_uploader' => $instance->getUserUploader(),
            'abstract' => $instance->getAbstract(),
            'language' => $instance->getLanguage(),
            'is_public' => $instance->getVisibility() == \KlinkVisibilityType::KLINK_PUBLIC,
            'authors' => is_array($instance->getAuthors()) ? implode(',', $instance->getAuthors()) : $instance->getAuthors(), //is Array so there is something that might be done
        ));
        
        return $cached;

    }

    public function getIsPublicAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function isMine()
    {
        return $this->file_id != null;
    }

    public function isRemoteWebPage(){
        return ($this->document_type == 'web-page' || $this->document_type == 'document') && !is_null($this->file) && starts_with($this->file->original_uri, 'http');
    }
    
    
    public function ago(){
        $diff = $this->updated_at->diffInDays();
        
        if($diff < 7){
            return $this->updated_at->diffForHumans();
        }
        
        return $this->updated_at->format(trans('units.date_format'));
    }
    
    public function isIndexed(){
        return $this->status !== self::STATUS_NOT_INDEXED && $this->status !== self::STATUS_ERROR;
    }
    
    
//    public function getUpdatedAtAttribute($value){
//        dd($value);
//    }
    
    public function getCreatedAt(){
        return $this->created_at->format(trans('units.date_format'));
    }
    
    public function setLastErrorAttribute($value){
        
        
        if(is_a($value, '\Exception')){
            $obj = new \stdClass;
            $obj->message = $value->getMessage();
            $obj->type = get_class($value);
            $obj->payload = $value;
            
            $value = $obj;
        }
        else if(is_object($value)){
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = get_class($value);
            $value = $obj;
        }
        else if(is_array($value)){
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = 'array';
            $value = $obj;
        }
        else if( is_string($value) || is_numeric($value)  || is_bool($value) ){
            $obj = new \stdClass;
            $obj->payload = $value;
            $obj->type = is_string($value) ? 'string' : (is_bool($value) ? 'boolean' : 'number');
            $value = $obj;
        }
        else if( !is_null($value) ){
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
    public function getLastErrorAttribute($value){
        
        return is_null($value) ? null : json_decode($value);
        
    }

}
