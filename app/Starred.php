<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

class Starred extends Model {
    /*
    id: bigIncrements
    user_id: User
    document_id: DocumentDescriptor
    created_at: date
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'starred';

    protected $fillable = ['user_id', 'document_id'];


    public function user(){
        
        // One to One
        return $this->hasOne('User');

    }

    /**
     * [documents description]
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany [description]
     */
    public function document()
    {
        return $this->belongsTo('KlinkDMS\DocumentDescriptor', 'document_id');
    }

    public function scopeOfUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeByDocumentId($query, $document_id)
    {
        return $query->where('document_id', $document_id);
    }


    public static function existsByDocumentAndUserId($document_id, $user_id)
    {
        return !is_null(self::ofUser($user_id)->byDocumentId($document_id)->first());
    }

    public static function getByDocumentAndUserId($document_id, $user_id)
    {
        return self::ofUser($user_id)->byDocumentId($document_id)->first();
    }


}
