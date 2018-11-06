<?php

namespace KBox\Pages;

use Markdown;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

/**
 *
 *
 * @property string $title
 * @property string $description
 * @property array $authors
 * @property string $created_at
 * @property string $updated_at
 * @property
 */
abstract class PageModel
{
    use HasAttributes, HasTimestamps;

    const PRIVACY_POLICY_FULL = 'privacy-full';
    const PRIVACY_POLICY_SUMMARY = 'privacy';
    const TERMS_OF_SERVICE = 'terms';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    protected static $disk = 'app';
    protected static $pathInDisk = 'pages';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $languageKey = 'language';
    
    /**
     * Indicate if page exists in the storage
     *
     * @var bool
     */
    protected $exists = false;

    public function __construct($attributes = [])
    {
        $this->fill($attributes);

        $this->updateTimestamps();
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function fillFromFileMeta($path)
    {
        $object = YamlFrontMatter::parse($this->getStorage()->get($path));

        $this->fill($object->matter());

        $this->exists = true;
        
        $this->syncOriginal();
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return $this->incrementing;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->primaryKey);
    }

    public function getQualifiedKeyName()
    {
        return $this->primaryKey;
    }

    public function getQualifiedLanguageName()
    {
        return $this->languageKey;
    }

    public function getRelationValue($key)
    {
        // required as the HasAttributes trait check for relations existence
        return null;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ! is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    public function setAuthorsAttribute($value)
    {
        $this->attributes['authors'] = array_wrap($value);
    }

    public function getIdAttribute($value)
    {
        return $value ?? ($this->title ? str_slug($this->title) : null);
    }

    protected function prepareFileContent()
    {
        return '$contents';
    }

    protected function getFileContentAsHtml()
    {
        $object = YamlFrontMatter::parse($this->getStorage()->get($this->getFilePath()));
        return Markdown::convertToHtml($object->body());
    }

    public function getHtmlAttribute($value)
    {
        return $this->getFileContentAsHtml();
    }

    /**
     * Return true if the Page exists in the storage
     */
    public function exists()
    {
        return $this->exists;
    }

    protected function getStorage()
    {
        return Storage::disk(static::$disk);
    }

    protected function getStoragePath()
    {
        $directory = static::$pathInDisk;

        $this->getStorage()->makeDirectory($directory);

        return $directory;
    }

    protected function getFilePath()
    {
        $language = $this->getAttribute($this->languageKey) ?? config('app.locale');
        return "{$this->getStoragePath()}/{$this->getKey()}.{$language}.md";
    }

    /**
     * Saves the page
     */
    public function save()
    {
        $storage = $this->getStorage();

        if ($this->isDirty()) {
            $storage->put($this->getFilePath(), $this->prepareFileContent());
    
            $this->syncOriginal();
            $this->exists = true;
        }

        return true;
    }

    /**
     * Deletes the page
     */
    public function delete()
    {
        if ($this->exists()) {
            $storage = $this->getStorage();
            $directory = $this->getStoragePath();
    
            $storage->delete($this->getFilePath());
    
            $this->exists = false;
        }

        return true;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the page Finder instance.
        $model = new static((array) $attributes);

        $model->exists = $exists;

        return $model;
    }

    public function newInstanceFromFile($path)
    {
        $model = new static();

        $model->fillFromFileMeta($path);

        return $model;
    }
    
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    protected static function getFinder()
    {
        return new Finder(new static, self::$disk, self::$pathInDisk);
    }

    /**
     * Find a page
     *
     * @param string $page
     * @param string $language
     * @return Page|null
     */
    public static function find($page, $language = null)
    {
        return static::getFinder()->find($page, $language);
    }

    /**
     * Retrieve all defined pages
     *
     * @return Collection
     */
    public static function all()
    {
        return static::getFinder()->get();
    }

    /**
     * Create a page
     *
     * @param array $attributes The page attributes. Default empty array, the page attributes will be initialized with creation and last update date
     */
    public static function create($attributes = [])
    {
        $instance = new static($attributes);

        return $instance;
    }

    public static function createFromFile($path)
    {
        $instance = new static();

        $instance->fillFromFileMeta($path);

        return $instance;
    }
}
