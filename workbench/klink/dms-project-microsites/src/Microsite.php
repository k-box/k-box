<?php namespace Klink\DmsMicrosites;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KlinkDMS\Project;
use KlinkDMS\User;

/**
  The Microsite model.
*/
class Microsite extends Model {

    use SoftDeletes;
    
    /*
      id,
      project_id
      title
      slug
      description
      logo
      hero_image
      default_language
      user_id
      institution_id
      created_at
      updated_at
      deleted_at
    */

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
     */
    public function project() {
        return $this->belongsTo('KlinkDMS\Project', 'project_id', 'id');
    }
    
    /**
     * The user that has created the microsite
     */
    public function institution() {
        return $this->belongsTo('KlinkDMS\Institution', 'institution_id', 'id');
    }
       
    /**
     * The user that has created the microsite
     */
    public function user() {
        return $this->belongsTo('KlinkDMS\User', 'user_id', 'id');
    }
    
    /**
     * relation with the MicrositeContent model
     */
    public function contents() {
        return $this->hasMany('Klink\DmsMicrosites\MicrositeContent', 'microsite_id', 'id');
    }
    
    /**
     * Retrieve the microsite pages
     *
     * @return Collection of MicrositeContent with type MicrositeContent::TYPE_PAGE
     */
    public function pages(){
        return $this->contents()->type(MicrositeContent::TYPE_PAGE)->get();
    }
    
    /**
     * Retrieve the microsite menus
     *
     * @return Collection of MicrositeContent with type MicrositeContent::TYPE_MENU
     */
    public function menus(){
        return $this->contents()->type(MicrositeContent::TYPE_MENU)->get();
    }


    /**
     * Scope for getting microsite by slug
     *
     * @param string $slug the slug to search for
     *
     * @return QueryBuilder
     */
    public function scopeSlug($query, $slug){
      return $query->where('slug', $slug);
    }
    
    
}
