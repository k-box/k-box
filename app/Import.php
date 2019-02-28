<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\Import
 *
 * @deprecated The Import entity is here only for backward compatibility. It will be removed in a future version
 *
 * @property int $id
 * @property int $bytes_expected
 * @property int $bytes_received
 * @property int $user_id
 * @property int $file_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $status
 * @property string $status_message
 * @property int $parent_id
 * @property bool $is_remote
 * @property string $message
 * @property array $payload
 * @property string $job_payload
 * @property-read \KBox\Import $father
 * @property-read \KBox\File $file
 * @property-read mixed $is_completed
 * @property-read mixed $is_error
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import allCompleted()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import allRoots()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import allZombies()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import completed($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import fromFile($file_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import fromUser($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import myChildren($parent_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import myDownloads($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import notCompleted($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereBytesExpected($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereBytesReceived($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereIsRemote($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereJobPayload($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import wherePayload($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereStatusMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Import withError($user_id)
 * @mixin \Eloquent
 */
class Import extends Model
{

    /**
     * SATUSES_*
     * status codes
     */
    const STATUS_QUEUED = 0;
    
    /**
     * Indicates that the import has been paused. This status can only be reached from the queued status before
     * the import will be processed. A running import cannot be put in a paused state
     */
    const STATUS_PAUSED = 6;

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
    const MESSAGE_PAUSED = "paused";
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

    message: text

    payload: json
    job_payload: Laravel serialized payload of the Job in the queue, not empty only in case of failures

    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'import';

    protected $dates = ['created_at','updated_at'];
    
    protected $casts = [
        'payload' => 'array',
    ];
    
    protected $hidden = [ 'payload', 'job_payload' ];
    
    protected $appends = ['is_error', 'is_completed'];

    public function file()
    {
        return $this->belongsTo(\KBox\File::class);
    }

    public function father()
    {
        return $this->hasOne(\KBox\Import::class);
    }
    /*
     * delete all the downloads updated 30 minutes ago
     */
    public function scopeAllZombies($query)
    {
        return $query->where('status', self::STATUS_STARTED)->where('updated_at', '>=', strtotime("-30 minutes"));
    }

    public function scopeCompleted($query, $user_id)
    {
        return $query->where('status', self::STATUS_COMPLETED)->where('user_id', $user_id);
    }

    public function scopeNotCompleted($query, $user_id)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED)->where('user_id', $user_id);
    }
    
    public function scopeAllCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    public function scopeMyDownloads($query, $user_id)
    {
        return $query->where('status', self::STATUS_DOWNLOADING)->where('user_id', $user_id);
    }
    public function scopeMyChildren($query, $parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }
    public function scopeWithError($query, $user_id)
    {
        return $query->where('status', self::STATUS_ERROR)->where('user_id', $user_id);
    }
    
    public function scopeFromUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }
    
    public function scopeFromFile($query, $file_id)
    {
        return $query->where('file_id', $file_id);
    }

    public function scopeAllRoots($query)
    {
        return $query->whereNull('parent_id');
    }
    
    public function isError()
    {
        return $this->status === self::STATUS_ERROR || $this->status === self::STATUS_ERROR_ALREADY_EXISTS;
    }
    
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    
    // accessor methods for adding is_error and is_completed in the serialized json
    public function getIsErrorAttribute()
    {
        return $this->isError();
    }
    
    public function getIsCompletedAttribute()
    {
        return $this->isCompleted();
    }
}
