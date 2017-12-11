<?php

namespace Klink\DmsMicrosites;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KBox\Project;
use KBox\User;

/**
 * The Microsite model.
 *
 * Fields:
 * - id,
 * - project_id
 * - title
 * - slug
 * - description
 * - logo
 * - hero_image
 * - default_language
 * - user_id
 * - created_at
 * - updated_at
 * - deleted_at
 * @uses SoftDeletes
 */
class Microsite extends Model
{
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'microsites';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['project_id', 'title', 'slug', 'user_id', 'description', 'institution_id', 'logo', 'hero_image', 'default_language'];
    

    /**
     * The user that has created the microsite
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('KBox\Project', 'project_id', 'id');
    }
       
    /**
     * The user that has created the microsite
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('KBox\User', 'user_id', 'id');
    }
    
    /**
     * relation with the MicrositeContent model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents()
    {
        return $this->hasMany('Klink\DmsMicrosites\MicrositeContent', 'microsite_id', 'id');
    }
    
    /**
     * Retrieve the microsite pages
     *
     * @return Collection of MicrositeContent with type MicrositeContent::TYPE_PAGE
     */
    public function pages()
    {
        return $this->contents()->type(MicrositeContent::TYPE_PAGE)->get();
    }
    
    /**
     * Retrieve the microsite menus
     *
     * @return Collection of MicrositeContent with type MicrositeContent::TYPE_MENU
     */
    public function menus()
    {
        return $this->contents()->type(MicrositeContent::TYPE_MENU)->get();
    }

    /**
     * Scope for get microsite by slug
     *
     * @param string $slug the slug to search for
     *
     * @return QueryBuilder
     */
    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
