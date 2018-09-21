<?php

namespace KBox\Documents\Properties;

use KBox\File;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Properties presenter
 *
 * Define how file properties are presented on the UI
 */
class Presenter implements Htmlable
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $properties;

    public function __construct($properties)
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
     * Create a properties presented instance
     *
     * @param File|Collection $properties
     * @return Presenter
     */
    public static function for($properties)
    {
        return new static($properties);
    }
}
