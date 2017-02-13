<?php namespace KlinkDMS;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use KlinkDMS\Traits\HasCapability;
use KlinkDMS\Traits\UserOptionsAccessor;
use Illuminate\Database\Eloquent\SoftDeletes;
use KlinkDMS\Traits\LocalizableDateFields;

/**
 * The project concept.
 */
class Project extends Model {

    use LocalizableDateFields;
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
    protected $fillable = ['name', 'description', 'user_id', 'collection_id', 'avatar'];
    
    
    /**
     * The root collection that stores the hierarchy of the project
     */
    public function collection() {
      
      return $this->hasOne('KlinkDMS\Group', 'id', 'collection_id');
    
    }
    
    /**
     * The relation with the user tha manages the project
     */
    public function manager(){
      return $this->belongsTo('KlinkDMS\User', 'user_id', 'id');
    }
    
    /**
     * The users that partecipates into the Project
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
    
    public function getDocumentsCount(){

        if(!$this->collection->hasChildren()){
          return $this->collection->documents()->count();
        }

        return $this->collection->getDescendants()->load('documents')->pluck('documents')->collapse()->count() + $this->collection->documents()->count();
        

    }
    
    /**
     * Test if a project is accessible by a user
     *
     * @param Project $project the project to test the accessibility
     * @param User $user the user to use for the testing
     * @return boolean true if the project can be accessed by $user
     */
    public static function isAccessibleBy(Project $project, User $user){

      if($user->isDMSManager()){
        return true;
      }

      // TODO: this can be optimized

      $managed = $user->managedProjects()->get(['projects.id'])->pluck('id')->toArray();

      $added_to = $user->projects()->get(['projects.id'])->pluck('id')->toArray();

			return in_array($project->id, $managed) || in_array($project->id, $added_to);

    }

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
