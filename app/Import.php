<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Import extends Model {

    /**
     * SATUSES_*
     * status codes
     */
    const STATUS_QUEUED = 0;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, is currently in indexing
     */
    const STATUS_DOWNLOADING = 1;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, has been indexed
     */
    const STATUS_COMPLETED = 2;

    const STATUS_INDEXING = 4;

    /**
     * Indicate that the document, reference by a DocumentDescriptor, cannot be indexed due to one or more errors
     */
    const STATUS_ERROR = 3;

    const STATUS_ERROR_ALREADY_EXISTS = 5;

    /**
     * MESSAGES_*
     * status message
     */
    const MESSAGE_QUEUED = "queued";
    const MESSAGE_DOWNLOADING = "downloading";
    const MESSAGE_COMPLETED = "completed";
    const MESSAGE_INDEXING = "indexing";
    const MESSAGE_ERROR = "error";
    const MESSAGE_ERROR_LOOSE_CHUNKS = "the file is not downloaded completely. try again.";
    const MESSAGE_ERROR_FILE_NOT_FOUND = "file not found";
    
    
    /*
    id: bigIncrements
    bytes_expected: bigInteger unsigned
    bytes_received: bigInteger unsigned
    status: integer
    user_id: User
    file_id: File
    status_message: string
    is_remote: boolean
    parent_id: Import => parent_id===0 ? root
    created_at
    updated_at
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'import';

    protected $dates = ['created_at','updated_at'];



    public function file()
    {
        return $this->belongsTo('\KlinkDMS\File');
    }



    public function father(){
        return $this->hasOne('\KlinkDMS\Import');
    }
    /*
     * delete all the downloads updated 30 minutes ago
     */
    public function scopeAllZombies($query)
    {
        return $query->where('status', self::STATUS_STARTED)->where('updated_at','>=',strtotime("-30 minutes"));
    }

    public function scopeCompleted($query,$user_id)
    {
        return $query->where('status', self::STATUS_COMPLETED)->where('user_id',$user_id);
    }

    public function scopeNotCompleted($query,$user_id)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED)->where('user_id',$user_id);
    }
    
    public function scopeAllCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    public function scopeMyDownloads($query,$user_id)
    {
        return $query->where('status', self::STATUS_DOWNLOADING)->where('user_id', $user_id);
    }
    public function scopeMyChildren($query,$parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }
    public function scopeWithError($query,$user_id)
    {
        return $query->where('status', self::STATUS_ERROR)->where('user',$user_id);
    }
    
    public function scopeFromUser($query,$user_id)
    {
    	return $query->where('user_id',$user_id);
    }
    
    public function scopeFromFile($query,$file_id)
    {
    	return $query->where('file_id',$file_id);
    }

    public function scopeAllRoots($query){
        return $query->whereNull('parent_id');
    }
}
