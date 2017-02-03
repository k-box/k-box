<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

/**
 * The dynamic configuration options
 *
 * fields:
 * - id: bigIncrements
 * - key:
 * - value:
 */
class Option extends Model {
    
    /**
     * The Map visualization setting field
     */
    const MAP_VISUALIZATION_SETTING = 'map_visualization';
    
    /**
     * K-Link Public Core settings field
     */
    const PUBLIC_CORE_ENABLED = 'public_core_enabled';
    const PUBLIC_CORE_URL = 'public_core_url';
    const PUBLIC_CORE_USERNAME = 'public_core_username';
    const PUBLIC_CORE_PASSWORD = 'public_core_password';
    const PUBLIC_CORE_DEBUG = 'public_core_debug';
    const PUBLIC_CORE_CORRECT_CONFIG = 'public_core_correct';
    const PUBLIC_CORE_NETWORK_NAME_EN = 'public_core_network_name_en';
    const PUBLIC_CORE_NETWORK_NAME_RU = 'public_core_network_name_ru';
    
    /**
     * The option that stores the key for the UserVoice support service
     */
    const SUPPORT_TOKEN = 'support_token';

    /**
     * The option that stores the Analytics token
     */
    const ANALYTICS_TOKEN = 'analytics_token';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'options';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    /**
     * Scope a query to include only options with the 
     * specified key
     *
     * @param string $key the option key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Find an option by its key
     * 
     * @param string $key the option key
     * @return Option|null the {@see Option} instance if found, null otherwise
     */
    public static function findByKey($key)
    {

        return self::fromKey($key)->first();
    }

    /**
     * Scope a query to include only options that 
     * are within a section.
     *
     * Options in a section are prefixed with the 
     * section name followed by a dot
     * e.g. mail.something, the something option is in the mail section
     *
     * @param string $section_name the section name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSection($query, $section_name)
    {
        return $query->where('key', 'like', e($section_name) .'.%');
    }


    /**
     * Get an entire option section as nested array
     * 
     * @param  string $section_name the name of the spection (e.g. dms or mail) Everything that is before a dot separator
     * @return array               The nested array in which the keys are the option names and the values are the option value
     */
    public static function sectionAsArray($section_name)
    {
        $items = Option::section('dms.reindex')->get(array('key', 'value'));

        if($items->isEmpty())
        {
          return array();
        }

        $flat = $items->toArray();

        $keys = array_fetch($flat, 'key');
        $values = array_fetch($flat, 'value');

        $non_flat = array();
        foreach (array_combine($keys, $values) as $key => $value)
        {
          array_set($non_flat, $key, $value);
        }

        return $non_flat;

    }

    /**
     * Get the option value if any, or the default value if specified
     * 
     * @param  string $name   the option name
     * @param  mixed $default The default value to use if the option does not exists (default: null)
     * @return mixed          the option value
     */
    public static function option($name, $default = null)
    {

        $first = Option::findByKey($name);

        if(is_null($first)){
            return $default;
        }

        return $first->value;

    }


    /**
     * Save an option.
     *
     * If the option with the same name exists only the value will be updated.
     * 
     * @param  string $name  the option name (supports dot notation for sections)
     * @param  string $value the new value for the option
     * @return Option        The saved Option instance
     */
    public static function put($name, $value)
    {
        $first = Option::findByKey($name);

        if(is_null($first)){
            return Option::create(array('key' => $name, 'value' => $value));
        }

        $first->value = $value;

        return $first->save();
    }

    /**
     * Removes an existing option
     * 
     * @param  string $name The option name
     * @return boolean       true in case of success, false otherwise
     */
    public static function remove($name)
    {
        $first = Option::findByKey($name);

        if(!is_null($first)){
            return $first->delete();
        }

        return true;
    }
    
    
    
    // convenience methods for known options
    
    /**
     * Check if the map visualization is enabled
     * 
     * @return boolean true in case the map visualization is enabled, false otherwise. The default value, if not configured explicetely, is enabled.
     */
    public static function is_map_visualization_enabled()
    {
        
        return static::option(self::MAP_VISUALIZATION_SETTING, true);
        
    }
    
    /**
     * Get the support service access token.
     *
     * First the static configuration is checked, then the stored options will be checked
     *
     * @return string|boolean the support ticket integration token if configured, false if not configured
     */
    public static function support_token(){
        $conf = config("dms.support_token");
        
        if(is_null($conf)){
            
            $opt = static::option( static::SUPPORT_TOKEN, false );
            
            return empty($opt) ? false : $opt;
        }
        
        return $conf;
    }

    /**
     * Get the analytics tracking token.
     *
     *
     * @return string|boolean the anlytics site id to be used in the Piwik analytics code
     */
    public static function analytics_token(){
            
        $opt = static::option( static::ANALYTICS_TOKEN, false );
            
        return empty($opt) ? false : $opt;
        
    }


}
