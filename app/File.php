<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * The representation of a File on disk
 *
 * @property int $id the incremental identifier of the file
 * @property int $user_id the id of the user that created the file for the first time
 * @property string $name the file name
 * @property string $hash the file content SHA-512 hash
 * @property int $size the file size in byte
 * @property \Carbon\Carbon $created_at when the file was created
 * @property \Carbon\Carbon $updated_at when the file was last updated
 * @property \Carbon\Carbon $deleted_at when the file was trashed
 * @property string $thumbnail_path the path of the thumbnail
 * @property string $path the file location on disk
 * @property string $original_uri the file original location, used only if the file was downloaded from a web server
 * @property int $revision_of the id of the previous version of the file
 * @property string $mime_type the file mime type
 * @property bool $is_folder if this was a folder being imported in the K-Box
 * @property-read \KlinkDMS\DocumentDescriptor $document the related Document Descriptor
 * @property-read \KlinkDMS\File $revisionOf the previous version of the File
 * @property-read \KlinkDMS\User $user The User that uploaded the file.
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File folders() {@see KlinkDMS\File::scopeFolders()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File fromHash($hash) {@see KlinkDMS\File::scopeFromHash()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File fromOriginalUri($uri) {@see KlinkDMS\File::scopeFromOriginalUri()}
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereHash($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereIsFolder($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereMimeType($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereOriginalUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereRevisionOf($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereThumbnailPath($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\File whereUserId($value)
 * @mixin \Eloquent
 */
class File extends Model {

    use SoftDeletes;

    /**
     * The mime types for which a preview is supported
     * 
     * @var array
     */
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
        'text/csv',
        'application/vnd.google-apps.document',
		'application/vnd.google-apps.presentation',
		'application/vnd.google-apps.spreadsheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    protected $dates = ['deleted_at'];

    /**
     * The user that uploaded the file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        
        // One to One
        return $this->belongsTo('\KlinkDMS\User');

    }

    /**
     * The DocumentDescriptor that links to this File.
     *
     * Can be null in case of File revision
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document(){
        
        return $this->belongsTo('\KlinkDMS\DocumentDescriptor', 'id', 'file_id');

    }

    /**
     * Get previous version of the File
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function revisionOf(){

        return $this->belongsTo('\KlinkDMS\File', 'revision_of', 'id');

    }

    /**
     * Get all versions of the File
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function revisionOfRecursive()
    {
       return $this->revisionOf()->with('revisionOfRecursive');
    }

    /**
     * Check if the file is supported into K-Link
     *
     * @return boolean true if the file is supported, false otherwise
     */
    public function isKlinkSupported()
    {
        return \KlinkDocumentUtils::isMimeTypeSupported( $this->mime_type );
    }
    
    /**
     * Check if the file could be indexed into the K-Search
     *
     * @return boolean true if the file can be indexed, false otherwise
     */
    public function isIndexable()
    {
        return \KlinkDocumentUtils::isMimeTypeIndexable( $this->mime_type );
    }
    
    /**
     * Check if the file is a web page and has a remote URI
     *
     * @return boolean if the File is a web page and was imported via url import
     */
    public function isRemoteWebPage(){
        return starts_with($this->mime_type, 'text/html') && starts_with($this->original_uri, 'http');
    }

    /**
     * Retrieve all files that have the specified {@see \KlinkDMS\File::$original_uri}
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $uri the URI to search for
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromOriginalUri($query, $uri)
    {
        return $query->where('original_uri', $uri);
    }

    /**
     * Retrieve file instances that are imported folders
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFolders($query)
    {
        return $query->where('is_folder', true);
    }

    /**
     * Load all files with a given hash value
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $hash the hash to filter for
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromHash($query, $hash){
        return $query->where('hash', $hash);
    }
    
    
    /**
     * Delete the file from the database and from the file system
     * Deletes also the thumbnail, if exists.
     */
    public function physicalDelete(){
        
        $is_folder = $this->is_folder;
        
        $file_path  = $this->path;
        $thumb_path = $this->thumbnail_path;
        
        $done = false;
        
        if($is_folder){
            $done = $this->forceDelete();
        }
        else {
        
            // real deletion
            $done = true;
            
            if(@is_file($file_path)){
                $done = @unlink($file_path);
            }
            
            if($done){
                @unlink($thumb_path);
                
                $done = $this->forceDelete();
            }
        }
        
        return !$this->exists;
    }

    /**
     * 
     *
     * @return boolean
     */
    public function canBePreviewed(){
        return in_array($this->attributes['mime_type'], $this->previewSupportedMime);
    }

    /**
     * Check if a file with a given hash exists
     *
     * @param string $hash
     * @return boolean
     */
    public static function existsByHash($hash){
        return !is_null(File::fromHash($hash)->first());
    }

    /**
     * Find a file by its hash value
     *
     * @param string $hash
     * @return \KlinkDMS\File
     * @throws ModelNotFoundException if a file with a given hash do not exists
     */
    public static function findByHash($hash){
        return File::fromHash($hash)->firstOrFail();
    }

    /**
     * Check if a file exists by a given hash and original source origin folder
     *
     * @param string $hash the file hash
     * @param string $source_folder the file original source
     * @return boolean
     */
    public static function existsByHashAndSourceFolder($hash, $source_folder){
        return !is_null(File::fromHash($hash)->fromOriginalUri($source_folder)->first());
    }

    private function flatten_revisions(File $file, &$revisions = array()){

        $revisions[] = $file;

        if(is_null($file->revision_of)){
            return $revisions;
        }
        else {
            return $this->flatten_revisions($file->revisionOf()->first(), $revisions);
        }

    }

    private function last_version_recursive(File $file){

        $belongsTo = $file->belongsTo('\KlinkDMS\File', 'id', 'revision_of')->first();

        if(!is_null($belongsTo)){

            return $this->last_version_recursive($belongsTo);
        }

        return $file;

    }

    /**
     * Return all the document versions that are older than the current file
     */
    public function getVersions(){

        $all = $this->flatten_revisions($this);

        return collect($all);

    }

    /**
     * The latest available version.
     *
     * Find and return the last known revision of this file
     */
    public function getLastVersion()
    {

        return $this->last_version_recursive($this);

    }

}
