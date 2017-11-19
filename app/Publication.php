<?php

namespace KlinkDMS;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Represent the detail of a publication action made on a specific DocumentDescriptor
 */
class Publication extends Model
{
    const STATUS_QUEUED = 'queued';
    // const STATUS_PROCESSING = 'processing';
    const STATUS_PUBLISHING = 'publishing';
    const STATUS_PUBLISHED = 'published';
    const STATUS_UNPUBLISHED = 'unpublished';
    const STATUS_UNPUBLISHING = 'unpublishing';
    const STATUS_FAILED = 'failed';

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at',
        'unpublished_at',
        'failed_at',
    ];

    protected $fillable = [
        'published_by',
        'published_at',
        'unpublished_by',
        'published_at',
        'unpublished_at',
        'pending',
        'streaming_url',
        'streaming_id',
    ];

    protected $casts = [
        'pending' => 'boolean',
    ];

    protected $append = [ 'status' ];

    /**
     * The DocumentDescriptor published by this Publication
     */
    public function document()
    {
        return $this->belongsTo('KlinkDMS\DocumentDescriptor', 'descriptor_id', 'id');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNull('unpublished_at')->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->wherePending(true);
    }

    public function getStatusAttribute($value = null)
    {
        if ($this->pending) {
            if (! $this->published) {
                return self::STATUS_PUBLISHING;
            } elseif (! $this->unpublished) {
                return self::STATUS_UNPUBLISHING;
            }

            return self::STATUS_QUEUED;
        }

        if ($this->published) {
            return self::STATUS_PUBLISHED;
        }
        if ($this->unpublished) {
            return self::STATUS_UNPUBLISHED;
        }
        if ($this->failed) {
            return self::STATUS_FAILED;
        }

        return null;
    }

    /**
     * Set the completed attribute
     *
     * @param  bool  $completed
     * @return void
     */
    public function setPublishedAttribute($published)
    {
        if ($published && ! $this->published_at) {
            $this->attributes['published_at'] = Carbon::now();
            $this->attributes['pending'] = false;
        }

        if (! $published && $this->published_at) {
            $this->attributes['published_at'] = null;
        }
    }

    /**
     * Get if the upload is complete.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getPublishedAttribute($value = null)
    {
        return isset($this->attributes['published_at']) &&
               ! is_null($this->attributes['published_at']) &&
               (! isset($this->attributes['unpublished_at']) ||
               isset($this->attributes['unpublished_at']) && is_null($this->attributes['unpublished_at']));
    }
    
    /**
     * Set the completed attribute
     *
     * @param  bool  $completed
     * @return void
     */
    public function setUnpublishedAttribute($unpublished)
    {
        if ($unpublished && ! $this->unpublished_at) {
            $this->attributes['unpublished_at'] = Carbon::now();
            $this->attributes['pending'] = false;
        }

        if (! $unpublished && $this->unpublished_at) {
            $this->attributes['unpublished_at'] = null;
        }
    }

    /**
     * Get if the upload is complete.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getUnpublishedAttribute($value = null)
    {
        return isset($this->attributes['unpublished_at']) && ! is_null($this->attributes['unpublished_at']);
    }
    
    /**
     * Set the completed attribute
     *
     * @param  bool  $completed
     * @return void
     */
    public function setFailedAttribute($failed)
    {
        if ($failed && ! $this->failed_at) {
            $this->attributes['failed_at'] = Carbon::now();
            $this->attributes['pending'] = false;
        }

        if (! $failed && $this->failed_at) {
            $this->attributes['failed_at'] = null;
        }
    }

    /**
     * Get if the upload is complete.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getFailedAttribute($value = null)
    {
        return isset($this->attributes['failed_at']) && ! is_null($this->attributes['failed_at']);
    }
}
