<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model {
    /*
    id: bigIncrements
    name: string
    hash: mediumText
    path: string
    mime_type: string
    user_id: User
    size: bigInteger (unsigned)
    revision_of: File (nullable)
    thumbnail_path: string
    original_uri: string nullable (The original source of the file (path, url,...))
    is_folder: boolean (default false)
    */

    use SoftDeletes;


    private $previewSupportedMime = array(
        'application/pdf', 
        'image/png', 
        'image/gif', 
        'image/jpg', 
        'image/jpeg',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'application/rtf',
        'text/x-markdown',
        'application/vnd.google-apps.document',
		'application/vnd.google-apps.presentation',
		'application/vnd.google-apps.spreadsheet',
        );

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    protected $dates = ['deleted_at'];

    public function user(){
        
        // One to One
        return $this->belongsTo('\KlinkDMS\User');

    }

    /**
     * Getting all the versions of the File
     * @return [type] [description]
     */
    public function revisionOf(){

        return $this->belongsTo('\KlinkDMS\File', 'revision_of', 'id');

    }

    // all ascendants
    public function revisionOfRecursive()
    {
       return $this->revisionOf()->with('revisionOfRecursive');
    }

    /**
     * Tells if the file is supported into K-Link
     * @return boolean true if the file is supported, false otherwise
     */
    public function isKlinkSupported()
    {
        return \KlinkDocumentUtils::isMimeTypeSupported( $this->mime_type );
    }
    
    /**
     * Tell if the file could be indexed into K-Link
     * @return boolean true if the file can be indexed, false otherwise
     */
    public function isIndexable()
    {
        return \KlinkDocumentUtils::isMimeTypeIndexable( $this->mime_type );
    }


    public function scopeFromOriginalUri($query, $uri)
    {
        return $query->where('original_uri', $uri);
    }

    public function scopeFolders($query){
        return $query->where('is_folder', true);
    }

    public function scopeFromHash($query, $hash){
        return $query->where('hash', $hash);
    }

    public function scopeVersionsOf($value='')
    {
        # code...
    }


    public function canBePreviewed(){
        return in_array($this->attributes['mime_type'], $this->previewSupportedMime);
    }


    public static function existsByHash($hash){
        return !is_null(File::fromHash($hash)->first());
    }

    public static function existsByHashAndSourceFolder($hash, $source_folder){
        return !is_null(File::fromHash($hash)->fromOriginalUri($source_folder)->first());
    }

}
