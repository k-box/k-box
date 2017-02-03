<?php namespace KlinkDMS\Traits;

use ReflectionClass;

/**
 * Add enumeration like behavior to available constants in a class.
 *
 * Better readability using Enumerators
 * http://themsaid.com/better-readability-enumerators-php-20160420/
 *
 * author Mohamed Said (themsaid on GitHub)
 * original source: https://gist.github.com/themsaid/593a1972adbe35150e730c0ad3632cad 
 */
trait HasEnums
{

    /**
     * The array of enumerators of a given group.
     *
     * @param null|string $group
     * @return array
     */
    static function enums($group = null)
    {
        $constants = (new ReflectionClass(get_called_class()))->getConstants();

        if ($group)
        {

            return array_filter($constants, function ($key) use ($group)
            {

                return strpos($key, $group.'_') === 0;

            }, ARRAY_FILTER_USE_KEY);

        }

        return $constants;
    }



    /**
     * Check if the given value is valid within the given group.
     *
     * @param mixed $value
     * @param null|string $group
     * @return bool
     */
    static function isValidEnumValue($value, $group = null)
    {
        $constants = static::enums($group);

        return in_array($value, $constants);
    }



    /**
     * Check if the given key exists.
     *
     * @param mixed $key
     * @return bool
     */
    static function isValidEnumKey($key)
    {
        return array_key_exists($key, static::enums());
    }

}
