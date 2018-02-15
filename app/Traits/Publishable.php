<?php

namespace KBox\Traits;

use KBox\User;
use KBox\Publication;
use KBox\Jobs\PublishDocumentJob;
use KBox\Jobs\UnPublishDocumentJob;

/**
 * Add support for managing publication
 */
trait Publishable
{
    /**
     * Retrive the associated Publication
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publications()
    {
        return $this->hasMany(Publication::class, 'descriptor_id', 'id');
    }

    /**
     * Retrieve publication
     */
    public function publication()
    {
        // considering the last publication added
        // other publications are likely to be history related and not the current one
        return $this->publications()->orderBy('id', 'desc')->first();
    }

    public function getPublicationAttribute($value = null)
    {
        return $this->publication();
    }

    /**
     * Publishes the resource
     */
    public function publish(User $by = null)
    {
        if ($this->hasPendingPublications() || $this->isPublished()) {
            return;
        }
        
        // create a publication entry for the document and the user
        $publish_request = new Publication([
            'published_by' => $by->id,
            'pending' => true
        ]);
        $this->publications()->save($publish_request);
        // mark document as public
        $this->is_public = true;
        $this->save();
        
        dispatch(new PublishDocumentJob($publish_request));
    }

    /**
     * Unpublishes the resource
     */
    public function unpublish(User $by = null)
    {
        if ($this->hasPendingPublications()) {
            return;
        }
        $unpublish_request = tap($this->publication(), function ($publication) use ($by) {
            $publication->update([
                'unpublished_by' => $by->id,
                'pending' => true,
                'failed_at' => null
            ]);
        });
        // mark document as private only
        $this->is_public = false;
        $this->save();

        dispatch(new UnPublishDocumentJob($unpublish_request));
    }
    
    public function hasPendingPublications()
    {
        return $this->publications()->pending()->count() > 0;
    }
    
    public function isPublished()
    {
        return $this->is_public && $this->publications()->published()->count() > 0;
    }

    public function isCopyrightOwnerValidForPublishing()
    {
        $owner = $this->copyright_owner->only(['name', 'email', 'website']);

        if ($owner->isEmpty() || $owner->values()->filter()->count() < 2) {
            return false;
        }

        return true;
    }
}
