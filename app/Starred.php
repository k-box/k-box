<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        return $this->belongsTo(User::class);
    }

    /**
     * The {@see DocumentDescriptor} that is starred
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(DocumentDescriptor::class, 'document_id')->withTrashed();
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

    public function scopeSortUsingSorter($query, Sorter $sorter)
    {
        if (is_null($sorter->column)) {
            return $query;
        }
        
        if (Str::startsWith($sorter->column, 'document_descriptors.')) {
            return $query->select('starred.*')
                ->join('document_descriptors', 'starred.document_id', '=', 'document_descriptors.id')
                ->orderBy($sorter->column, $sorter->order);
        }

        return $query->orderBy($sorter->column, $sorter->order);
    }

    public static function sortableFields()
    {
        return [
            // field on the database, type, field on the search engine
            'starred_date' => ['created_at', 'date', null],
            'update_date' => ['document_descriptors.updated_at', 'date', 'properties.updated_at'],
            'creation_date' => ['document_descriptors.created_at', 'date', 'properties.created_at'],
            'name' => ['document_descriptors.title', 'string', 'properties.title'],
            'type' => ['document_descriptors.document_type', 'string', null],
            'language' => ['document_descriptors.language', 'string', 'properties.language'],
        ];
    }
}
