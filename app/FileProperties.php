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
     * Create a new file properties instance
     */
    public function __construct($attributes = [])
    {
        parent::__construct(array_dot($attributes));
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

        return new self($raw->toArray());
    }
}
