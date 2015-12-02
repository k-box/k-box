<?php namespace KlinkDMS;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use KlinkDMS\Traits\HasCapability;
use KlinkDMS\Traits\UserOptionsAccessor;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

  use Authenticatable, CanResetPassword, HasCapability, SoftDeletes, UserOptionsAccessor;

  const OPTION_LIST_TYPE = "list_style";
  
  const OPTION_LANGUAGE = "language";


  /*

      $table->bigIncrements('id');
      $table->string('name');
      $table->string('email')->unique();
      $table->string('password', 60);
      $table->string('avatar')->nullable();
      $table->rememberToken();
      $table->timestamps();

   */

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'users';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'email', 'password', 'institution_id'];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['password', 'remember_token'];

  public function scopeFromName($query, $name) {
    return $query->where('name', $name);
  }


  public function scopeFromEmail($query, $mail) {
    return $query->where('email', $mail);
  }


  public static function findByName($name) {

    return self::fromName($name)->first();
  }


  public static function findByEmail($email) {

    return self::fromEmail($email)->first();
  }


  /**
   * Retrive the associated Capabilities
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function searches() {

    return $this->hasMany('KlinkDMS\RecentSearch');

  }

  /**
   * [groups description]
   */
  public function groups() {
    // return $this->belongsToMany('KlinkDMS\Group', 'document_groups');
    return $this->hasMany('KlinkDMS\Group');

  }
  
  /**
    Groups that I've created'
  */
  public function peoplegroups() {
    return $this->hasMany('KlinkDMS\PeopleGroup');
  }
  
  /**
    Groups that I was inserted into
  */
  public function involvedingroups() {
    return $this->belongsToMany('KlinkDMS\PeopleGroup', 'peoplegroup_to_user', 'user_id', 'peoplegroup_id');
  }

  public function shares(){
    return $this->hasMany('KlinkDMS\Shared', 'user_id');
  }

  public function institution(){
    return $this->hasOne('KlinkDMS\Institution', 'id', 'institution_id');
  }

  /**
   * [starred description]
   * @return [type] [description]
   */
  public function documents() {
    return $this->hasMany('KlinkDMS\DocumentDescriptor', 'owner_id');
  }

  /**
   * [starred description]
   * @return [type] [description]
   */
  public function starred() {
    return $this->hasMany('KlinkDMS\Starred');
  }

  public function options() {
    return $this->hasMany('KlinkDMS\UserOption');
  }


  public function projects() {
    return $this->belongsToMany('KlinkDMS\Project', 'userprojects', 'user_id', 'project_id');
  }
  
  public function managedProjects() {
    return $this->hasMany('KlinkDMS\Project');
  }

    /**
     * Generates a random eight characters password.
     * 
     * @return an eight character password (not hashed)
     */
    public static function generatePassword()
    {
        return str_random(8);
    }

    /**
      Gets the user institution
    */
    public function getInstitution(){
        return !is_null($this->institution) ? $this->institution->id : null;
    }
    
    
    public function getInstitutionName(){
        return !is_null($this->institution) ? $this->institution->name : '';
    }


  /**
  *
  */
  public function optionListStyle(){
    $opt = $this->getOption(self::OPTION_LIST_TYPE, null);


    return  (!is_null($opt)) ? $opt->value : 'cards';
  }

  // option kqy, value
  public function getOption($name, $default = null)
  {
    $first = $this->options()->option($name)->first();

    return  (!is_null($first)) ? $first : $default;
  }

  public function setOption($name, $value='')
  {

    \Log::info('Calling User::setOption ', ['key' => $name, 'value' => $value]);

    $first = $this->getOption($name, null);

    if(is_null($first)){
      $this->options()->save(new UserOption(['key' => $name, 'value' => $value]));
    }
    else {
      $first->value = $value;
      $first->save();
    }


  }



  // Navigation path and routes
  
  public function homeRoute(){
    
    $search = $this->can(Capability::MAKE_SEARCH);
    $see_share = $this->can(Capability::RECEIVE_AND_SEE_SHARE);
    $partner = $this->canAll(Capability::$PARTNER); 
    
    if($this->isDMSManager()){
			// full manager redirect to the dashboard
			return route('administration.index'); // '/home';
		}
		else if($this->isContentManager() || 
				$this->can(Capability::UPLOAD_DOCUMENTS) ||
				$this->can(Capability::EDIT_DOCUMENT)){
					
			//documents manager redirect to documents
			return route('documents.index');
		}
    else if($search && !$see_share){
      return route('search');
    }
    else if(!$search && $see_share){
      return route('shares.index');
    }
		else {
			//poor child redirect to search
			return route('search');
		}
  }
    
}
