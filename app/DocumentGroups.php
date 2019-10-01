<?php

namespace KBox;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DocumentGroups extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'added_by',
    ];

    /**
     * The user that added the document to the group
     *
     * @return \KBox\User|null
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
