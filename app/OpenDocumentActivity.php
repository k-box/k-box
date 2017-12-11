<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\OpenDocumentActivity
 *
 * @deprecated
 * @property int $id
 * @property int $document_id
 * @property int $originating_activity
 * @property-read \KBox\DocumentDescriptor $documentDescriptor
 * @method static \Illuminate\Database\Query\Builder|\KBox\OpenDocumentActivity whereDocumentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\OpenDocumentActivity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\OpenDocumentActivity whereOriginatingActivity($value)
 * @mixin \Eloquent
 */
class OpenDocumentActivity extends Model
{
    /*
    id: bigIncrements
    document_id: DocumentDescriptor
    originating_activity: Activity
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'open_document_activities';

    public $timestamps = false;

    public function documentDescriptor()
    {
        
        // One to One
        return $this->hasOne('KBox\DocumentDescriptor');

        // One to Many
        // return $this->hasMany('DocumentDescriptor');
        // return $this->hasMany('DocumentDescriptor', 'document_id', 'id');
        
        // Many to Many
        // return $this->belongsToMany('DocumentDescriptor');
        // return $this->belongsToMany('DocumentDescriptor', 'PIVOT_TABLE'); //last is pivot table name
    }
}
