<?php namespace Klink\DmsMicrosites;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KlinkDMS\Project;
use KlinkDMS\User;

/**
 * The Microsite Content model.
 *
 * Fields:
 * - id,
 * - microsite_id
 * - language
 * - title
 * - slug
 * - content
 * - type
 * - user_id
 * - created_at
 * - updated_at
 * - deleted_at
 *
 * @uses SoftDeletes
 */
class MicrositeContent extends Model {

    use SoftDeletes;
    
    /**
     * The type of the content for a page
     */
    const TYPE_PAGE = 1;
    
    /**
     * The type of the content for a menu
     */
    const TYPE_MENU = 2;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'microsite_contents';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['microsite_id', 'language', 'content', 'user_id', 'title', 'slug', 'type'];


    /**
     * Type Scope.
     *
     * Restrict to content whose type is equal to the specified type
     *
     * @param integer $type the content type (e.g. MicrositeContent::TYPE_PAGE)
     */
    public function scopeType($query, $type){
      return $query->where('type', $type);
    }
    
    /**
     * Language Scope.
     *
     * Restrict to content whose language is equal to the specified language
     *
     * @param string $language the content language code (e.g. en)
     */
    public function scopeLanguage($query, $language){
      return $query->where('language', $language);
    }
    
}
