<?php

namespace KBox;

use ReflectionClass;
use ReflectionException;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

/**
 * File properties
 *
 * Collection of additional metadata extracted from the file and stored in the
 * database, as a json object.
 *
 * Multi level array access is performed using dot notation
 */
class FileProperties extends Fluent
{

    /**
     * Exclude attribute from being serialized with dot notation
     */
    protected $exclude = [];
    
    /**
     * Create a new file properties instance
     */
    public function __construct($attributes = [])
    {
        parent::__construct($this->processInput($attributes));
    }

    private function processInput($attributes)
    {
        // serialize with dot notation only the
        // attributes that are not excluded

        $dotted = array_dot(collect($attributes)->except($this->exclude));
        $preserve = collect($attributes)->only($this->exclude);

        return $preserve->merge($dotted);
    }

    /**
     * Get the localized attribute name
     *
     * @param string the attribute key
     * @return string the localized
     */
    public function labelFor($key)
    {
        return $key;
    }

    /**
     * Get an attribute from the container.
     * 
     * If a class property with the same name exists, the property is preferred
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if(property_exists($this, $key)){
            return $this->$key;
        }

        return parent::get($key, $default);
    }

    /**
     * Merge additional properties
     * 
     * @param Collection|array $additional
     * @return $this
     */
    public function merge($additional)
    {
        $inputs = $this->processInput($additional);

        $this->attributes = collect($this->attributes)->merge($inputs)->toArray();

        return $this;
    }

    /**
     * Convert the FileProperties to array
     *
     * used for serialization
     *
     * @return array
     */
    public function toArray()
    {
        return array_prepend($this->attributes, get_class($this), '@class');
    }

    /**
     * Dump the content of the properties for debug
     * 
     * @uses the dump() helper
     * @return $this
     */
    public function dump()
    {
        dump($this->toArray());

        return $this;
    }

    /**
     * Create a FileProperties instance from a collection
     *
     * @param Collection the collection that contains the raw attributes
     * @return FileProperties
     */
    public static function fromCollection(Collection $raw)
    {
        if ($raw->get('@class', null)) {
            $propertiesClass = $raw->get('@class');

            try {
                (new ReflectionClass($propertiesClass))->isSubclassOf(FileProperties::class);
                return new $propertiesClass($raw->except('@class')->toArray());
            } catch (ReflectionException $ex) {
            }
        }

        return new static($raw->toArray());
    }
}
