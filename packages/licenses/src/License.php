<?php

namespace OneOffTech\Licenses;

use App;
use Markdown;
use OneOffTech\Licenses\Concerns\HasAttributes;

/**
 * Describe a license entity
 *
 * @property-read string id the SPDX identifier of the license, except for Public domain and Copyright which returns PD and C respectively, as both are not valid SPDX licenses
 * @property-read string name the SPDX defined license name
 * @property-read string title The localized license title
 * @property-read string short_title the short title version of the license, if available
 * @property-read string description The localized license description
 * @property-read string icon The icon file content (currently only SVG code)
 * @property-read string icon_path The path on disk where the icon file is located
 * @property-read string description_path  The path on disk where the description file, in English, is located
 */
class License /*implements ArrayAccess, Arrayable, Jsonable, JsonSerializable*/
{
    use HasAttributes;

    private $assetsPath = './';

    /**
     * Create a new License model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes($attributes);
    }

    /**
     * Get the localized license title.
     *
     * @param array $value the title attribute
     * @return string the title of the license with respect to the application locale. If locale is not supported the English version is returned
     */
    public function getTitleAttribute($value)
    {
        $locale = App::getLocale();
        $fallback = config('app.fallback_locale');

        if (isset($value[$locale])) {
            return $value[$locale];
        }

        if (isset($value[$fallback])) {
            return $value[$fallback];
        }

        return isset($value['en']) ? $value['en'] : null;
    }

    public function getShortTitleAttribute($value)
    {
        return isset($this->attributes['short_title']) ? $this->attributes['short_title'] : $this->attributes['name'];
    }

    public function getIconAttribute($icon_file)
    {
        $path = $this->icon_path;

        return $icon_file && is_file($path) ? file_get_contents($path) : null;
    }

    public function getIconPathAttribute($value)
    {
        return isset($this->attributes['icon']) && $this->attributes['icon'] ? "$this->assetsPath/icons/{$this->attributes['icon']}" : null;
    }
    
    public function getDescriptionAttribute($value)
    {
        $path = $this->description_path;

        return $path && is_file($path) ? @file_get_contents($path) : null;
    }
    
    public function getHtmlDescriptionAttribute($value)
    {
        return Markdown::convertToHtml($this->description ?? '');
    }

    public function getDescriptionPathAttribute($value)
    {
        $locale = App::getLocale();
        $fallback = config('app.fallback_locale');

        $locale_path = "$this->assetsPath/descriptions/$locale/{$this->attributes['description']}";
        $fallback_path = "$this->assetsPath/descriptions/$fallback/{$this->attributes['description']}";

        if (is_file($locale_path)) {
            return $locale_path;
        }

        if (is_file($fallback_path)) {
            return $fallback_path;
        }

        return null;
    }

    public function getLicenseAttribute($value)
    {
        $locale = App::getLocale();
        $fallback = config('app.fallback_locale');

        if (isset($value[$locale])) {
            return $value[$locale];
        }

        if (isset($value[$fallback])) {
            return $value[$fallback];
        }

        return isset($value['en']) ? $value['en'] : null;
    }

    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return ! is_null($this->getAttribute($key));
    }

    public function getKeyName()
    {
        return 'id';
    }

    /**
     * Set the absolute path of the assets folder that contains the
     * icons and the descriptions of the licenses
     *
     * @param string $path
     * @return License
     */
    public function setAssetsPath($path)
    {
        $this->assetsPath = $path;

        return $this;
    }
}
