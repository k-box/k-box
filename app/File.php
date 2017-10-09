<?php

namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Avvertix\TusUpload\TusUpload;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Dyrynda\Database\Support\GeneratesUuid;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use Klink\DmsAdapter\KlinkDocumentUtils;

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
 * @property string $thumbnail_path the path of the thumbnail relative to the storage disk
 * @property string $path the file location inside the storage disk
 * @property-read string $absolute_path the file absolute path on disk
 * @property-read string $absolute_thumbnail_path the thumbnail file absolute path on disk
 * @property string $original_uri the file original location, used only if the file was downloaded from a web server
 * @property int $revision_of the id of the previous version of the file
 * @property string $mime_type the file mime type
 * @property bool $is_folder if this was a folder being imported in the K-Box
 * @property-read \KlinkDMS\DocumentDescriptor $document the related Document Descriptor
 * @property-read \KlinkDMS\File $revisionOf the previous version of the File
 * @property-read \KlinkDMS\User $user The User that uploaded the file.
 * @property \Carbon\Carbon $upload_completed_at when the upload finished
 * @property \Carbon\Carbon $upload_cancelled_at when the upload was cancelled by the user
 * @property \Carbon\Carbon $upload_started_at when the upload started, might be the same as the creation date
 * @property-read bool $upload_completed true if the upload completed
 * @property-read bool $upload_cancelled true if the upload was cancelled by the user
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
class File extends Model
{
    use SoftDeletes, GeneratesUuid;

    /**
     * The mime types for which a preview is supported
     *
     * @var array
     */
    private $previewSupportedMime = [
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
        'video/mp4',
        ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    protected $dates = ['deleted_at', 'upload_started_at', 'upload_completed_at'];

    protected $casts = [
        'uuid' => 'uuid'
    ];

    /**
     * The user that uploaded the file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        
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
    public function document()
    {
        return $this->belongsTo('\KlinkDMS\DocumentDescriptor', 'id', 'file_id');
    }

    /**
     * Get previous version of the File
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function revisionOf()
    {
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
        return KlinkDocumentUtils::isMimeTypeSupported($this->mime_type);
    }
    
    /**
     * Check if the file could be indexed into the K-Search
     *
     * @return boolean true if the file can be indexed, false otherwise
     */
    public function isIndexable()
    {
        return KlinkDocumentUtils::isMimeTypeIndexable($this->mime_type);
    }
    
    /**
     * Check if the file is a web page and has a remote URI
     *
     * @return boolean if the File is a web page and was imported via url import
     */
    public function isRemoteWebPage()
    {
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
    public function scopeFromHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }
    
    /**
     * Get the upload job, if still stored, that made possible to
     * have this file in the system.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function upload()
    {
        return $this->hasOne(TusUpload::class, 'request_id', 'request_id');
    }

    /**
     * Get if the upload is complete.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getUploadCompletedAttribute($value = null)
    {
        return isset($this->attributes['upload_completed_at']) && ! is_null($this->attributes['upload_completed_at']);
    }

    /**
     * Get if the upload was cancelled.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getUploadCancelledAttribute($value = null)
    {
        return isset($this->attributes['upload_cancelled_at']) && ! is_null($this->attributes['upload_cancelled_at']);
    }

    /**
     * Set the upload_started attribute
     *
     * @param  bool  $started
     * @return void
     */
    public function setUploadStartedAttribute($started)
    {
        if ($started && ! $this->upload_started_at) {
            $this->attributes['upload_started_at'] = Carbon::now();
        }

        if (! $started && $this->upload_started_at) {
            $this->attributes['upload_started_at'] = null;
        }
    }

    /**
     * Get if the upload is started.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getUploadStartedAttribute($value = null)
    {
        return isset($this->attributes['upload_started_at']) && ! is_null($this->attributes['upload_started_at']);
    }

    /**
     * Get absolute_path attribute value.
     *
     * @param  mixed  $value not taken into account
     * @return string
     */
    public function getAbsolutePathAttribute($value = null)
    {
        if (@is_file($this->path)) {
            return $this->path;
        }
        return Storage::disk('local')->path($this->path);
    }

    /**
     * Get path attribute value.
     *
     * The path is relative to the storage disk
     *
     * @param  mixed  $value the path saved in the database
     * @return string the relative path of the file in the storage disk
     */
    public function getPathAttribute($value)
    {
        $disk_path = rtrim(Storage::disk('local')->path('/'), '/');

        if (starts_with($value, $disk_path)) {
            return ltrim(str_replace($disk_path, '', $value), '/');
        }

        return $value;
    }
    
    /**
     * Get thumbnail path attribute value.
     *
     * The path is relative to the storage disk
     *
     * @param  mixed  $value the thumbnail_path saved in the database
     * @return string the relative path of the thumbnail file in the storage disk
     */
    public function getThumbnailPathAttribute($value)
    {
        $disk_path = rtrim(Storage::disk('local')->path('/'), '/');

        if (starts_with($value, $disk_path)) {
            return ltrim(str_replace($disk_path, '', $value), '/');
        }

        return $value;
    }
    
    /**
     * Set thumbnail path attribute value.
     *
     * Set the path to null causes the thumbnail to be deleted.
     * Set the path to a different value than the current one causes the old thumbnail file to be deleted
     *
     * @param  mixed  $value the thumbnail_path saved in the database
     * @return void
     */
    public function setThumbnailPathAttribute($value)
    {
        if (! isset($this->attributes['thumbnail_path'])) {
            $this->attributes['thumbnail_path'] = $value;
        } elseif (! is_null($this->attributes['thumbnail_path'])) {
            $storage = Storage::disk('local');

            if (is_null($value)) {
                // delete thumbnail
    
                if (! starts_with($this->attributes['thumbnail_path'], rtrim(public_path('/'), '/'))) {
                    @$storage->delete($this->thumbnail_path);
                }
    
                $this->attributes['thumbnail_path'] = $value;
            } elseif ($this->attributes['thumbnail_path'] !== $value) {
                if (! starts_with($this->attributes['thumbnail_path'], rtrim(public_path('/'), '/'))) {
                    @$storage->delete($this->thumbnail_path);
                }
    
                $this->attributes['thumbnail_path'] = $value;
            }
        }
    }

    /**
     * Get absolute_thumbnail_path attribute value.
     *
     * @param  mixed  $value not taken into account
     * @return string
     */
    public function getAbsoluteThumbnailPathAttribute($value = null)
    {
        $disk_path = rtrim(public_path('/'), '/');
        $path = $this->thumbnail_path;

        if (is_null($path)) {
            return null;
        }
        
        if (starts_with($path, $disk_path)) {
            return $path;
        }
        return Storage::disk('local')->path($path);
    }
    
    /**
     * Force a hard delete on a soft deleted model.
     * Deleted also the physical file from the disk
     *
     * @return bool|null
     */
     public function forceDelete()
     {
         $this->forceDeleting = true;
        
         $deleted = $this->delete();

         $this->forceDeleting = false;
         
         if ($deleted) {
             $this->physicalDelete();
         }
 
         return $deleted;
     }

    /**
     * Delete the file from the database and from the file system
     * Deletes also the thumbnail, if exists.
     */
    protected function physicalDelete()
    {
        $storage = Storage::disk('local');
        
        $this->thumbnail_path = null;

        if ($this->uuid && basename(dirname($this->path)) === $this->uuid) {
            @$storage->deleteDirectory(dirname($this->path));
        } else {
            @$storage->delete($this->path);
        }
    }

    /**
     *
     *
     * @return boolean
     */
    public function canBePreviewed()
    {
        return in_array($this->attributes['mime_type'], $this->previewSupportedMime);
    }

    /**
     * Check if a file with a given hash exists
     *
     * @param string $hash
     * @return boolean
     */
    public static function existsByHash($hash)
    {
        return ! is_null(File::fromHash($hash)->first());
    }

    /**
     * Find a file by its hash value
     *
     * @param string $hash
     * @return \KlinkDMS\File
     * @throws ModelNotFoundException if a file with a given hash do not exists
     */
    public static function findByHash($hash)
    {
        return File::fromHash($hash)->firstOrFail();
    }

    /**
     * Check if a file exists by a given hash and original source origin folder
     *
     * @param string $hash the file hash
     * @param string $source_folder the file original source
     * @return boolean
     */
    public static function existsByHashAndSourceFolder($hash, $source_folder)
    {
        return ! is_null(File::fromHash($hash)->fromOriginalUri($source_folder)->first());
    }

    private function flatten_revisions(File $file, &$revisions = [])
    {
        $revisions[] = $file;

        if (is_null($file->revision_of)) {
            return $revisions;
        } else {
            return $this->flatten_revisions($file->revisionOf()->first(), $revisions);
        }
    }

    private function last_version_recursive(File $file)
    {
        $belongsTo = $file->belongsTo('\KlinkDMS\File', 'id', 'revision_of')->first();

        if (! is_null($belongsTo)) {
            return $this->last_version_recursive($belongsTo);
        }

        return $file;
    }

    /**
     * Return all the document versions that are older than the current file
     */
    public function getVersions()
    {
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

    /**
     * Create a file given an upload
     *
     * @param \Illuminate\Http\UploadedFile $upload the file uploaded
     * @param \KlinkDMS\User $uploader The user that perfomed the upload
     * @param \KlinkDMS\File $revision_of The file that will be replaced with this new uploaded version
     * @return \KlinkDMS\File
     * @throws \KlinkDMS\Exceptions\FileAlreadyExistsException if a file with the same hash already exists in the system
     */
    public static function createFromUploadedFile(UploadedFile $upload, User $uploader, File $revision_of = null)
    {
        try {
            $storage = Storage::disk('local');
            
            $file_model = new File();

            $uuid = $file_model->resolveUuid()->toString();

            $destination_path = date('Y').'/'.date('m').'/'.$uuid;

            $filename = $upload->getClientOriginalName();

            $file_m_time = false; //$upload->getMTime(); // will work?

            // move the file from the temp upload dir to the local storage
            $file_path = $upload->store($destination_path, 'local');

            // Get the absolute path of the file to use the hash_file function as Storage drivers don't support getting the hash of a file content
            // not using configuration value for local disk as during tests may vary if Storage::fake() is used
            $file_absolute_path = $storage->path($file_path);

            $hash = KlinkDocumentUtils::generateDocumentHash($file_absolute_path);

            if (static::existsByHash($hash)) {
                $storage->deleteDirectory($destination_path);

                $f = static::findByHash($hash);

                $descr = $f->getLastVersion()->document;
                
                throw new FileAlreadyExistsException($filename, $descr, $f);
            }

            $mime = $upload->getMimeType();

            $file_model->name = $filename;
            $file_model->uuid = $uuid;
            $file_model->hash = $hash;
            $file_model->mime_type=$mime;
            $file_model->size = $storage->size($file_path);
            $file_model->thumbnail_path = null;
            $file_model->path = $file_path;
            $file_model->user_id = $uploader->id;
            $file_model->original_uri = $file_path;
            $file_model->is_folder = false;
            $file_model->upload_started_at = \Carbon\Carbon::now();
            $file_model->upload_completed_at = \Carbon\Carbon::now();
            
            if ($file_m_time) {
                $file_model->created_at = \Carbon\Carbon::createFromFormat('U', $file_m_time);
            }

            if (! is_null($revision_of)) {
                $file_model->revision_of = $revision_of->id;
            }

            $file_model->save();

            return $file_model->fresh();
        } catch (\Exception $ex) {
            \Log::error('Create File from UploadFile failed', ['context' => 'DocumentsService@createFileFromUpload', 'upload' => $upload->getClientOriginalName(), 'owner' => $uploader->id, 'error' => $ex]);

            throw $ex;
        }
    }
}
