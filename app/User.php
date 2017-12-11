<?php

namespace KBox;

use Illuminate\Foundation\Auth\User as Authenticatable;

use KBox\Traits\HasCapability;
use KBox\Traits\UserOptionsAccessor;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use KBox\Notifications\ResetPasswordNotification;

/**
 * The User model
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $avatar
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $organization_name
 * @property string $organization_website
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Capability[] $capabilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\DocumentDescriptor[] $documents
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\KBox\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\PeopleGroup[] $involvedingroups
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Project[] $managedProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\UserOption[] $options
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\PeopleGroup[] $peoplegroups
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Project[] $projects
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\RecentSearch[] $searches
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Shared[] $shares
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Starred[] $starred
 * @method static \Illuminate\Database\Query\Builder|\KBox\User fromEmail($mail)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User fromName($name)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereInstitutionId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, HasCapability, SoftDeletes, UserOptionsAccessor;

    const OPTION_LIST_TYPE = "list_style";
  
    const OPTION_LANGUAGE = "language";
  
    const OPTION_TERMS_ACCEPTED = "terms_accepted";
  
    const OPTION_PERSONAL_IN_PROJECT_FILTERS = "show_personal_in_project_filters";
  
    const OPTION_ITEMS_PER_PAGE = "items_per_page";
  
    const OPTION_RECENT_RANGE = "recent_range";

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

    /**
     * Scope a query to include only users with the
     * specified name
     *
     * @param string $name the user name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope a query to include only users with the
     * specified email
     *
     * @param string $mail the user email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromEmail($query, $mail)
    {
        return $query->where('email', $mail);
    }

    /**
     * Search a user by name
     *
     * @param string $name
     * @return User|null the user, if any, null otherwise
     */
    public static function findByName($name)
    {
        return self::fromName($name)->first();
    }

   /**
    * Search a user by email
    *
    * @param string $name
    * @return User|null the user, if any, null otherwise
    */
    public static function findByEmail($email)
    {
        return self::fromEmail($email)->first();
    }

   /**
    * The searches performed by the user
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function searches()
    {
        return $this->hasMany('KBox\RecentSearch');
    }

   /**
    * User's collections
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function groups()
    {
        return $this->hasMany('KBox\Group');
    }
  
   /**
    * Groups of people created by the user
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function peoplegroups()
    {
        return $this->hasMany('KBox\PeopleGroup');
    }
  
   /**
    * Groups of people the user is inserted into
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function involvedingroups()
    {
        return $this->belongsToMany('KBox\PeopleGroup', 'peoplegroup_to_user', 'user_id', 'peoplegroup_id');
    }

   /**
    * The shares created by the user
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function shares()
    {
        return $this->hasMany('KBox\Shared', 'user_id');
    }

   /**
    * The institution of the user
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    * @deprecated Institutions will be removed, use the origanization_* fields instead
    */
    public function institution()
    {
        return $this->hasOne('KBox\Institution', 'id', 'institution_id');
    }

   /**
    * The documents uploaded by the user
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function documents()
    {
        return $this->hasMany('KBox\DocumentDescriptor', 'owner_id');
    }

   /**
    * the starred documents
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function starred()
    {
        return $this->hasMany('KBox\Starred');
    }

    /**
     * The user options
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany('KBox\UserOption');
    }

    /**
     * The projects in which the user is a member
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany('KBox\Project', 'userprojects', 'user_id', 'project_id');
    }
  
    /**
     * The projects managed by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managedProjects()
    {
        return $this->hasMany('KBox\Project');
    }

    /**
     * Generates a random eight characters password.
     *
     * @return string an eight character password (not hashed)
     */
    public static function generatePassword()
    {
        return str_random(8);
    }

    /**
     * Get the user institution identifier
     *
     * @return int|null the institution id, if configured, null otherwise
     * @deprecated Institutions will be removed, use the origanization_* fields instead
     */
    public function getInstitution()
    {
        return ! is_null($this->institution) ? $this->institution->id : null;
    }
    
    /**
     * Get the user institution name
     *
     * @return string the institution name, if configured, otherwise an empty string is returned
     * @deprecated Institutions will be removed, use the origanization_* fields instead
     */
    public function getInstitutionName()
    {
        return ! is_null($this->institution) ? $this->institution->name : '';
    }

    /**
     * Get the user preference for the style of the documents list
     *
     * @return string the list style preference. If not configured the return value is `cards`
     */
    public function optionListStyle()
    {
        $opt = $this->getOption(self::OPTION_LIST_TYPE, null);

        return  (! is_null($opt)) ? $opt->value : 'cards';
    }
  
    /**
     * Get if the user has accepted the terms
     *
     * @return bool true if the user accepted the terms of use. Default false
     */
    public function optionTermsAccepted()
    {
        $opt = $this->getOption(self::OPTION_TERMS_ACCEPTED, null);

        return  (! is_null($opt)) ? $opt->value : false;
    }

    /**
     * Retrieves the number of items to show per page for the pagination
     *
     * @return int the number of elements to show per page.
     *             Default value is retrieved from static configuration `dms.items_per_page`
     */
    public function optionItemsPerPage()
    {
        $opt = $this->getOption(self::OPTION_ITEMS_PER_PAGE, null);

        return  (! is_null($opt)) ? $opt->value : config('dms.items_per_page');
    }

    /**
     * Get the user preference for the recent documents range
     *
     * @return string the recent range. Default value is `currentweek` if the unified search is active, otherwise `currentmonth`
     */
    public function optionRecentRange()
    {
        $opt = $this->getOption(self::OPTION_RECENT_RANGE, null);

        return  (! is_null($opt)) ? $opt->value : 'currentweek';
    }

    /**
     * Set the number of items to show per page (for the pagination)
     *
     * @param int $itemsPerPage the number of items that defines a page
     */
    public function setOptionItemsPerPage($itemsPerPage)
    {
        $value = filter_var($itemsPerPage, FILTER_VALIDATE_INT);

        if ($value < 1 || $value > 100) {
            throw new \InvalidArgumentException(trans('validation.between.numeric', ['min' => 1, 'max' => 100]));
        }

        $opt = $this->setOption(self::OPTION_ITEMS_PER_PAGE, $value);

        return $this;
    }

    /**
     * Get the user preference about showing personal collections in filters
     *
     * @return bool true if personal collection can be shown in the collections filter. Default false
     */
    public function optionPersonalInProjectFilters()
    {
        $opt = $this->getOption(self::OPTION_PERSONAL_IN_PROJECT_FILTERS, null);

        return  (! is_null($opt)) ? $opt->value : false;
    }

    /**
     * Get a saved option by key
     *
     * @param string $name the option name/key
     * @param mixed $default the default value to return in case the option is not configured
     * @return UserOption|mixed the {@see UserOption} instance if found, $default otherwise
     */
    public function getOption($name, $default = null)
    {
        $first = $this->options()->option($name)->first();

        return  (! is_null($first)) ? $first : $default;
    }

    /**
     * Set a user preference value
     *
     * @param string $name the preference name
     * @param mixed $value the value for the preference
     */
    public function setOption($name, $value = '')
    {
        \Log::info('Calling User::setOption ', ['key' => $name, 'value' => $value]);

        $first = $this->getOption($name, null);

        if (is_null($first)) {
            $this->options()->save(new UserOption(['key' => $name, 'value' => $value]));
        } else {
            $first->value = $value;
            $first->save();
        }
    }

    /**
     * Determine the route to show after the login
     *
     * @return string the route url
     */
    public function homeRoute()
    {
        $search = $this->can_capability(Capability::MAKE_SEARCH);
        $see_share = $this->can_capability(Capability::RECEIVE_AND_SEE_SHARE);
        $partner = $this->can_all_capabilities(Capability::$PARTNER);
    
        if ($this->isDMSManager()) {
            return route('documents.index');
        } elseif ($this->isContentManager() ||
                $this->can_capability(Capability::UPLOAD_DOCUMENTS) ||
                $this->can_capability(Capability::EDIT_DOCUMENT)) {
            //documents manager redirect to documents
            return route('documents.index');
        } elseif ($search && ! $see_share) {
            return route('search');
        } elseif (! $search && $see_share) {
            return route('shares.index');
        } else {
            //poor child redirect to search
            return route('search');
        }
    }
    
    
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    
    
    
    
    
    public static function boot()
    {
        parent::boot();

        /**
         * Event when the user model is saved
         */
        static::saved(function ($user) {
            \Cache::forget('dms_project_collections-'.$user->id);
            \Cache::forget('dms_personal_collections'.$user->id);
            return $user;
        });
    }
}
