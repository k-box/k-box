<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OneOffTech\TusUpload\TusUpload;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Services\PreviewService;
use Dyrynda\Database\Support\GeneratesUuid;
use KBox\Documents\Facades\Files;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Illuminate\Support\Facades\Crypt;
use KBox\Events\FileDeleted;
use KBox\Events\FileDeleting;
use KBox\Events\FileRestored;
use KBox\Traits\ScopeNullUuid;

/**
 * The representation of a File on disk
 *
 * @property int $id the incremental identifier of the file
 * @property \Ramsey\Uuid\Uuid $uuid the UUID that identify the file
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
 * @property-read string $document_type the type of the file according to the K-Box categorization
 * @property string $original_uri the file original location, used only if the file was downloaded from a web server
 * @property int $revision_of the id of the previous version of the file
 * @property string $mime_type the file mime type
 * @property bool $is_folder if this was a folder being imported in the K-Box
 * @property \Illuminate\Support\Collection $properties additional properties of the file, as a collection
 * @property-read \KBox\DocumentDescriptor $document the related Document Descriptor
 * @property-read \KBox\File $revisionOf the previous version of the File
 * @property-read \KBox\User $user The User that uploaded the file.
 * @property \Carbon\Carbon $upload_completed_at when the upload finished
 * @property \Carbon\Carbon $upload_cancelled_at when the upload was cancelled by the user
 * @property \Carbon\Carbon $upload_started_at when the upload started, might be the same as the creation date
 * @property-read bool $upload_completed true if the upload completed
 * @property-read bool $upload_cancelled true if the upload was cancelled by the user
 * @method static \Illuminate\Database\Query\Builder|\KBox\File folders() {@see KBox\File::scopeFolders()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\File fromHash($hash) {@see KBox\File::scopeFromHash()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\File fromOriginalUri($uri) {@see KBox\File::scopeFromOriginalUri()}
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereHash($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereIsFolder($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereMimeType($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereOriginalUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereRevisionOf($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereThumbnailPath($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\File whereUserId($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    use SoftDeletes, GeneratesUuid, ScopeNullUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    protected $dates = ['deleted_at', 'upload_started_at', 'upload_completed_at'];

    protected $casts = [
        'uuid' => 'uuid',
        'properties' => 'collection',
    ];

    protected $dispatchesEvents = [
        'deleted' => FileDeleted::class,
        'deleting' => FileDeleting::class,
        'restored' => FileRestored::class,
    ];

    /**
     * The user that uploaded the file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // One to One
        return $this->belongsTo(\KBox\User::class);
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
        return $this->belongsTo(\KBox\DocumentDescriptor::class, 'id', 'file_id');
    }

    /**
     * Get previous version of the File
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function revisionOf()
    {
        return $this->belongsTo(\KBox\File::class, 'revision_of', 'id');
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
     * Get the previous versions of the current file
     *
     * @return Illuminate\Support\Collection|File[] the previous versions of the current file instance
     */
    public function versions()
    {
        if (! $this->revision_of) {
            return collect();
        }

        $current = $this->revisionOf()->first();
        $versions = collect();

        while (! is_null($current)) {
            $versions->push($current);
            $current = $current ? $current->revisionOf()->first() : null;
        }
        return $versions;
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
     * Retrieve all files that have the specified {@see \KBox\File::$original_uri}
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
                $this->deleteThumbnail($this->attributes['thumbnail_path']);
                
                $this->attributes['thumbnail_path'] = $value;
            } elseif ($this->attributes['thumbnail_path'] !== $value) {
                // delete old thumbnail file
                $this->deleteThumbnail($this->attributes['thumbnail_path']);
    
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
     * Get document_type attribute value.
     *
     * @param  mixed  $value not taken into account
     * @return string
     */
    public function getDocumentTypeAttribute($value = null)
    {
        list($mime, $documentType) = Files::recognize($this->absolute_path);
        return $documentType;
    }
    
    /**
     * Get the properties attribute
     *
     * @return \KBox\FileProperties
     */
    public function getPropertiesAttribute($value = null)
    {
        // transform the stored properties from json to collection
        // and encapsulate them in the FileProperties hierarchy

        $raw = $this->castAttribute('properties', $value);

        return $raw ? FileProperties::fromCollection($raw) : new FileProperties();
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
        $this->deleteThumbnail($this->thumbnail_path);

        if (empty($this->path)) {
            return;
        }
        $storage = Storage::disk('local');

        if ($this->uuid && basename(dirname($this->path)) === $this->uuid) {
            @$storage->deleteDirectory(dirname($this->path));
        } else {
            @$storage->delete($this->path);
        }
    }

    private function deleteThumbnail($path)
    {
        if (empty($path)) {
            return;
        }
        if (! starts_with($path, rtrim(public_path('/'), '/'))) {
            $storage = Storage::disk('local');
            @$storage->delete($path);
        }
    }

    /**
     * Check the file can be previewed
     *
     * @see \KBox\Documents\Services\PreviewService::isSupported
     * @return boolean
     */
    public function canBePreviewed()
    {
        return app(PreviewService::class)->isSupported($this);
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
     * @return \KBox\File
     * @throws ModelNotFoundException if a file with a given hash do not exists
     */
    public static function findByHash($hash)
    {
        return File::fromHash($hash)->firstOrFail();
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
        $belongsTo = $file->belongsTo(\KBox\File::class, 'id', 'revision_of')->first();

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
     * Check if file is a video
     *
     * @return bool true if file is a MP4 video, false otherwise
     */
    public function isVideo()
    {
        return $this->mime_type === 'video/mp4';
    }

    /**
     * Generate a token to be used to direct download this file
     *
     * @param int $duration The number of minutes this token will be valid. Default 5 minutes
     */
    public function generateDownloadToken($duration = 5)
    {
        $now = Carbon::now();

        $components = [
            $this->uuid,
            $this->hash,
            $now->timestamp,
            $now->copy()->addMinutes($duration ?? 5)->timestamp,
        ];

        return Crypt::encryptString(implode('#', $components));
    }

    /**
     * Resources related to video streaming.
     *
     * @return \Illuminate\Support\Collection A collection containing the dash manifest and the various resolutions the video is available in. If the file is not a video an empty collection will be returned
     */
    public function videoResources()
    {
        if (! $this->isVideo()) {
            return collect([]);
        }

        $files = collect(Storage::disk('local')->files(dirname($this->path)));
        // todo: files now return an array of SplFileInfo

        $resources = collect();

        $dash_manifest = $files->filter(function ($value, $key) {
            return ends_with($value, 'mpd');
        })->first();

        $resources->put('dash', $dash_manifest);

        $videos = $files->filter(function ($value, $key) {
            return ends_with($value, 'mp4') && $this->path !== $value;
        });

        $resources->put('streams', $videos);

        return $resources;
    }

    /**
     * Create a file given an upload
     *
     * @param \Illuminate\Http\UploadedFile $upload the file uploaded
     * @param \KBox\User $uploader The user that perfomed the upload
     * @param \KBox\File $revision_of The file that will be replaced with this new uploaded version
     * @return \KBox\File
     */
    public static function createFromUploadedFile(UploadedFile $upload, User $uploader, File $revision_of = null)
    {
        try {
            $storage = Storage::disk('local');
            
            $file_model = new File();

            $uuid = $file_model->resolveUuid()->toString();

            $destination_path = date('Y').'/'.date('m').'/'.$uuid;

            $filename = $upload->getClientOriginalName();

            $file_m_time = false; //$upload->getMTime();

            // double checking, guess the mime type and evaluate the mime type from
            // the list of known mime types, if different use the known one
            $guessed_mime_type = $upload->getMimeType();

            list($fallback_mime_type, $documentType) = Files::guessTypeFromExtension($filename);
            
            $mime = $fallback_mime_type === $guessed_mime_type ? $guessed_mime_type : $fallback_mime_type;
            
            $hash_name = substr($upload->hashName(), 0, 40).'.'.Files::extensionFromType($mime, $documentType); // because Laravel generates a 40 chars random name

            // move the file from the temp upload dir to the local storage
            $file_path = $upload->storeAs($destination_path, $hash_name, 'local');

            // Get the absolute path of the file to use the hash_file function as Storage drivers don't support getting the hash of a file content
            // not using configuration value for local disk as during tests may vary if Storage::fake() is used
            $file_absolute_path = $storage->path($file_path);

            $hash = Files::hash($file_absolute_path);

            list($recognizedMimeType, $recognizedDocumentType) = Files::recognize($file_absolute_path);

            $file_model->name = $filename;
            $file_model->uuid = $uuid;
            $file_model->hash = $hash;
            $file_model->mime_type=$recognizedMimeType;
            $file_model->size = $storage->size($file_path);
            $file_model->thumbnail_path = null;
            $file_model->path = $file_path;
            $file_model->user_id = $uploader->id;
            $file_model->original_uri = $file_path;
            $file_model->is_folder = false;
            $file_model->upload_started_at = Carbon::now();
            $file_model->upload_completed_at = Carbon::now();
            
            if ($file_m_time) {
                $file_model->created_at = Carbon::createFromFormat('U', $file_m_time);
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

    /**
     * Create a file entry from a given upload
     *
     * @param \Illuminate\Http\UploadedFile $file the uploaded file
     * @param \KBox\User $uploader User that perfomed the upload
     * @param \KBox\File $revision_of the previously uploaded version of the same file, if any. Default null.
     * @return \KBox\File
     */
    public static function fromUpload(UploadedFile $file, User $uploader, File $revision_of = null)
    {
        return static::createFromUploadedFile($file, $uploader, $revision_of);
    }
}
