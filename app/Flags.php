<?php

namespace KBox;

use KBox\Traits\HasEnums;
use InvalidArgumentException;

/**
 * The feature Flags.
 *
 * You can enable, disable and check the status of all the available feature
 * flags.
 *
 * Feature flags are persisted as {@see Option} with the "flag_" prefix.
 *
 * @uses KBox\Traits\HasEnums
 */
final class Flags
{
    use HasEnums;

    /**
     * The Plugins feature flag
     */
    const PLUGINS = 'plugins';
    
    /**
     * Check if a flag is enabled
     *
     * @param string $flag the flag name
     * @return boolean true if the flag is enabled, false otherwise
     */
    public static function isEnabled($flag)
    {
        if (! is_string($flag)) {
            throw new InvalidArgumentException("Flag name must be a string.");
        }

        if (! self::isValidEnumValue($flag)) {
            throw new InvalidArgumentException("Flag name $flag is invalid.");
        }

        $val = Option::option('flag_'.$flag, false);

        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Check if a flag is disabled
     *
     * @param string $flag the flag name
     * @return boolean true if the flag is disabled, false otherwise
     */
    public static function isDisabled($flag)
    {
        if (! is_string($flag)) {
            throw new InvalidArgumentException("Flag name must be a string.");
        }

        if (! self::isValidEnumValue($flag)) {
            throw new InvalidArgumentException("Flag name $flag is invalid.");
        }

        $val = Option::option('flag_'.$flag, false);

        return ! filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Enable a flag
     *
     * @param string $flag the flag name
     * @return boolean true if the flag was enabled succesfully, false otherwise
     */
    public static function enable($flag)
    {
        if (! is_string($flag)) {
            throw new InvalidArgumentException("Flag name must be a string.");
        }

        if (! self::isValidEnumValue($flag)) {
            throw new InvalidArgumentException("Flag name $flag is invalid.");
        }
    
        Option::put('flag_'.$flag, true);

        return true;
    }
    
    /**
     * Disable a flag
     *
     * @param string $flag the flag name
     * @return boolean true if the flag was disabled succesfully, false otherwise
     */
    public static function disable($flag)
    {
        if (! is_string($flag)) {
            throw new InvalidArgumentException("Flag name must be a string.");
        }

        if (! self::isValidEnumValue($flag)) {
            throw new InvalidArgumentException("Flag name $flag is invalid.");
        }
    
        Option::put('flag_'.$flag, false);

        return true;
    }

    /**
     * Toggle a flag.
     * If is enabled, it will be disabled.
     * If is disabled, it will be enabled.
     *
     * @param string $flag the flag name
     * @return boolean the current enable status. True if is enabled after the toggle,
     * false if disabled
     */
    public static function toggle($flag)
    {
        if (! is_string($flag)) {
            throw new InvalidArgumentException("Flag name must be a string.");
        }

        if (! self::isValidEnumValue($flag)) {
            throw new InvalidArgumentException("Flag name $flag is invalid.");
        }
    
        if (self::isEnabled($flag)) {
            self::disable($flag);
            return false;
        }
        
        self::enable($flag);
        return true;
    }

    /**
     * Get the enable/disable state of the Unified Search feature
     *
     * @return bool unified search enable status
     */
    public function isUnifiedSearchEnabled()
    {
        return true; //self::isEnabled(self::UNIFIED_SEARCH);
    }

    public function __call($method, $arguments)
    {
        if (str_is('is*Enabled', $method)) {
            $keys = self::constants();
            $key = strtolower(str_before(str_after($method, 'is'), 'Enabled'));
            if (self::isValidEnumKey(strtoupper($key))) {
                return static::isEnabled($key);
            }
        }
    }

    public static function __callStatic($method, $arguments)
    {
        if (str_is('is*Enabled', $method)) {
            $keys = self::constants();
            $key = strtolower(str_before(str_after($method, 'is'), 'Enabled'));
            if (self::isValidEnumKey(strtoupper($key))) {
                return static::isEnabled($key);
            }
        }
    }
}
