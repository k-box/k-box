<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a starred {@see DocumentDescriptor}
 *
 * Fields:
 * - id: bigIncrements
 * - user_id: User
 * - document_id: DocumentDescriptor
 * - created_at: date
 *
 * @property int $id
 * @property int $user_id
 * @property int $document_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \KBox\DocumentDescriptor $document
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred byDocumentId($document_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred ofUser($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred whereDocumentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Starred whereUserId($value)
 * @mixin \Eloquent
 */
class Starred extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'starred';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['user_id', 'document_id'];

    /**
     * The user that added the star
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('KBox\User');
    }

    /**
     * The {@see DocumentDescriptor} that is starred
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo('KBox\DocumentDescriptor', 'document_id');
    }

    /**
     * Scope the query to contain only stars of a
     * specific user
     *
     * @param string|int $user_id the user ID (primary key)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope the query to contain only a specific document
     *
     * @param string|int $document_id the document ID (primary key)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDocumentId($query, $document_id)
    {
        return $query->where('document_id', $document_id);
    }

    /**
     * Check if a star exists for a document and a user
     *
     * @param string|int $document_id the document descriptor identifier
     * @param string|int $user_id the user identifier
     * @return bool
     */
    public static function existsByDocumentAndUserId($document_id, $user_id)
    {
        return ! is_null(self::ofUser($user_id)->byDocumentId($document_id)->first());
    }

    /**
     * Get the star by document and user
     *
     * @param string|int $document_id the document descriptor identifier
     * @param string|int $user_id the user identifier
     * @return Starred|null
     */
    public static function getByDocumentAndUserId($document_id, $user_id)
    {
        return self::ofUser($user_id)->byDocumentId($document_id)->first();
    }
}
