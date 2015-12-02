<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

class Option extends Model {
    /*
    id: bigIncrements
    key:
    value:
    */
    
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


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'options';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];


    public function scopeFromKey($query, $key) {
        return $query->where('key', $key);
    }

    public static function findByKey($key) {

        return self::fromKey($key)->first();
    }


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

        if($items->isEmpty()){
          return array();
        }


        $flat = $items->toArray();

        $keys = array_fetch($flat, 'key');
        $values = array_fetch($flat, 'value');

        $non_flat = array();
        foreach (array_combine($keys, $values) as $key => $value) {
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
    public static function option($name, $default = null){

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
    public static function is_map_visualization_enabled(){
        
        return static::option(self::MAP_VISUALIZATION_SETTING, true);
        
    }


}
