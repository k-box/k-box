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
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
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
     * The DocumentDescriptor that links to this File.
     *
     * Can be null in case of File revision
     */
    public function document(){
        
        return $this->belongsTo('\KlinkDMS\DocumentDescriptor', 'id', 'file_id');

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
    
    /**
     * Check if the file is a web page and has a remote URI
     */
    public function isRemoteWebPage(){
        return $this->mime_type === 'text/html' && starts_with($this->original_uri, 'http');
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


    public function canBePreviewed(){
        return in_array($this->attributes['mime_type'], $this->previewSupportedMime);
    }


    public static function existsByHash($hash){
        return !is_null(File::fromHash($hash)->first());
    }

    public static function findByHash($hash){
        return File::fromHash($hash)->firstOrFail();
    }

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
    public function getLastVersion(){

        // return $this->belongsTo('\KlinkDMS\File', 'id', 'revision_of')->first();

        return $this->last_version_recursive($this);

        // return $this->belongsTo('\KlinkDMS\File', 'id', 'revision_of');

        // $all = $this->flatten_revisions($this);

        // return collect($all);

    }

    // 

}
