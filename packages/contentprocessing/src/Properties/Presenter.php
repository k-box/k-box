<?php

namespace KBox\Documents\Properties;

use KBox\File;
use ReflectionClass;
use ReflectionException;
use KBox\FileProperties;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Properties presenter
 *
 * Define how file properties are presented on the UI
 */
class Presenter implements Htmlable
{
    /**
     * @var \KBox\FileProperties
     */
    protected $properties;

    /**
     * The properties section title
     *
     * Default null, no section title will be printed
     *
     * @var string
     */
    protected $title = null;

    public function __construct(FileProperties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Get the HTML representation
     *
     * @return string
     */
    public function toHtml()
    {
        return '';
    }

    /**
     * Create a properties presenter instance
     *
     * If the properties define a @class, a class with Presenter suffix after the properties class is searched. If found is instantiated and returned
     *
     * @param FileProperties $properties
     * @return Presenter
     */
    public static function for(FileProperties $properties)
    {
        if ($properties->getClass()) {
            $propertiesClass = $properties->getClass();
            $presenterClass = "{$propertiesClass}Presenter";

            try {
                if (class_exists($presenterClass) &&
                    (new ReflectionClass($presenterClass))->isSubclassOf(Presenter::class)) {
                    return new $presenterClass($properties);
                }
            } catch (ReflectionException $ex) {
            }
        }

        return new static($properties);
    }
}
