<?php

namespace KBox;

use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;

class DuplicateDocument extends Model implements Htmlable
{
    protected $table = 'duplicate_descriptors';
    
    protected $dates = ['notification_sent_at', 'resolved_at'];

    protected $fillable = ['user_id','duplicate_document_id', 'document_id', 'resolved_at'];

    /**
     * Set if the user has been notified
     *
     * @param  bool  $started
     * @return void
     */
    public function setSentAttribute($sent)
    {
        if ($sent && ! $this->notification_sent_at) {
            $this->attributes['notification_sent_at'] = Carbon::now();
        }

        if (! $sent && $this->notification_sent_at) {
            $this->attributes['notification_sent_at'] = null;
        }
    }

    /**
     * Get if user has been notified
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getSentAttribute($value = null)
    {
        return isset($this->attributes['notification_sent_at']) && ! is_null($this->attributes['notification_sent_at']);
    }

    /**
     * Set if the user has resolved the duplication with an action
     *
     * @param  bool  $started
     * @return void
     */
    public function setResolvedAttribute($resolved)
    {
        if ($resolved && ! $this->resolved_at) {
            $this->attributes['resolved_at'] = Carbon::now();
        }

        if (! $resolved && $this->resolved_at) {
            $this->attributes['resolved_at'] = null;
        }
    }

    /**
     * Get if user has resolved the duplication
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getResolvedAttribute($value = null)
    {
        return isset($this->attributes['resolved_at']) && ! is_null($this->attributes['resolved_at']);
    }

    /**
     * Get a message representing the duplicate.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getMessageAttribute($value = null)
    {
        if (is_null($this->document) || is_null($this->duplicateOf)) {
            return '';
        }

        $args = [
            'duplicate_link' => RoutingHelpers::preview($this->document),
            'duplicate_title' => e($this->document->title),
            'existing_link' => RoutingHelpers::preview($this->duplicateOf),
            'existing_title' => e($this->duplicateOf->title),
        ];

        if ($this->document->owner_id === $this->user_id && $this->duplicateOf->owner_id === $this->user_id) {
            return trans('documents.duplicates.message_me_owner', $args);
        }

        $service = app('Klink\DmsDocuments\DocumentsService');

        $collections = $service->getDocumentCollections($this->duplicateOf, $this->user);

        if (! $collections->isEmpty()) {
            return trans('documents.duplicates.message_in_collection', array_merge($args, [
                'owner' => e($this->duplicateOf->owner->name),
                'collections' => $collections->map(function ($c) {
                    return '<a href="'.route('documents.groups.show', [ 'id' => $c->id, 'highlight' => $this->duplicateOf->id]).'">'.e($c->name).'</a>';
                })->implode(', ')
            ]));
        } else {
            return trans('documents.duplicates.message_with_owner', array_merge($args, [
                'owner' => e($this->duplicateOf->owner->name)
            ]));
        }

        return '';
    }

    /**
     * Return the HTML short message representation of this duplicate
     */
    public function toHtml()
    {
        return new HtmlString($this->message);
    }

    /**
     * The user that uploaded, and therefore triggered, the duplicate document finding
     *
     * @return \KBox\User
     */
    public function user()
    {
        return $this->belongsTo(\KBox\User::class, 'user_id', 'id');
    }
    
    /**
     * The document that caused the duplicate finding
     *
     * @return \Kbox\DocumentDescriptor
     */
    public function document()
    {
        return $this->belongsTo(\KBox\DocumentDescriptor::class, 'duplicate_document_id', 'id')->withTrashed();
    }
    
    /**
     * The existing document in the system
     *
     * @return \Kbox\DocumentDescriptor
     */
    public function duplicateOf()
    {
        return $this->belongsTo(\KBox\DocumentDescriptor::class, 'document_id', 'id')->withTrashed();
    }

    /**
     * Filter by user
     * @param KBox\User|in $user
     */
    public function scopeOf($query, $user)
    {
        $id = is_a($user, \KBox\User::class) ? $user->id : $user;

        return $query->where('user_id', $id);
    }
    
    /**
     * Filter for not sent notifications
     *
     */
    public function scopeNotSent($query)
    {
        return $query->whereNull('notification_sent_at');
    }
    
    /**
     * Filter for not sent notifications
     *
     */
    public function scopeNotResolved($query)
    {
        return $query->whereNull('resolved_at');
    }
}
