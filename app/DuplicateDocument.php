<?php

namespace KBox;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DuplicateDocument extends Model
{
    protected $table = 'duplicate_descriptors';
    
    protected $dates = ['notification_sent_at'];

    protected $fillable = ['user_id','duplicate_document_id', 'document_id'];



    
    /**
     * Set the upload_started attribute
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
     * Get if the upload is started.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getSentAttribute($value = null)
    {
        return isset($this->attributes['notification_sent_at']) && ! is_null($this->attributes['notification_sent_at']);
    }

    /**
     * Get a message representing the duplicate.
     *
     * @param  mixed  $value not taken into account
     * @return bool
     */
    public function getMessageAttribute($value = null)
    {
        return "duplicate message $this->id";
    }




    /**
     * The user that uploaded, and therefore triggered, the duplicate document finding
     * 
     * @return \KBox\User
     */
    public function user()
    {
        return $this->belongsTo('KBox\User', 'user_id', 'id');
    }
    
    /**
     * The document that caused the duplicate finding
     * 
     * @return \Kbox\DocumentDescriptor
     */
    public function document()
    {
        return $this->belongsTo('KBox\DocumentDescriptor', 'duplicate_document_id', 'id');
    }
    
    /**
     * The existing document in the system
     * 
     * @return \Kbox\DocumentDescriptor
     */
    public function duplicateOf()
    {
        return $this->belongsTo('KBox\DocumentDescriptor', 'document_id', 'id');
    }


    /**
     * Filter by user
     * @param KBox\User|in $user
     */
    public function scopeOf($query, $user)
    {
        $id = is_a($user, 'KBox\User') ? $user->id : $user;

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
}
