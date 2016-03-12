<?php namespace KlinkDMS;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use KlinkDMS\Traits\HasCapability;
use KlinkDMS\Traits\UserOptionsAccessor;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
  The project concept.
*/
class Project extends Model {


  /*

      $table->bigIncrements('id');
      $table->string('name');
      $table->text('description')->nullable();
      $table->string('avatar')->nullable();
      
      $table->bigInteger('user_id')->unsigned(); --> project manager
      $table->bigInteger('collection_id')->unsigned(); --> root project collection
      
      $table->timestamps();

   */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'user_id', 'collection_id'];
    
    
    /**
     * The root collection that stores the hierarchy of the project
     */
    public function collection() {
      
      return $this->hasOne('KlinkDMS\Group', 'id', 'collection_id');
    
    }
    
    /**
      The relation with the user tha manages the project
    */
    public function manager(){
      return $this->belongsTo('KlinkDMS\User', 'user_id', 'id');
    }
    
    /**
      The users that partecipates into the Project
    */
    public function users() {
      return $this->belongsToMany('KlinkDMS\User', 'userprojects', 'project_id', 'user_id');
    }

    public function scopeManagedBy($query, $user){
      return $query->where('user_id', $user);
    }    
    
    /**
     * The associated microsite
     */
    public function microsite(){
        return $this->hasOne('\Klink\DmsMicrosites\Microsite');
    }
    
    // /**
    //  * 
    //  * @return [type] [description]
    //  */
    // public function documents() {
    //   return $this->hasMany('KlinkDMS\DocumentDescriptor', 'owner_id');
    // }
    

    static function boot()
    {
        parent::boot();

        static::saved(function ($project)
        {
            
            \Cache::forget('dms_project_collections-' . $project->user_id);
            
            $affected_users = $project->users()->get();
            
            foreach ($affected_users as $u) {
                \Cache::forget('dms_project_collections-' . $u->id);
            }
            
            return $project;

        });
    }
}
