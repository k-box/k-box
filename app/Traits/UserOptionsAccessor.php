<?php namespace KlinkDMS\Traits;


use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use KlinkDMS\UserOption;

/**
 * Add support for faster accessing of the user's saved options
 */
trait UserOptionsAccessor
{

    
    /**
     * Get the language option for the specified user
     *
     * @param mixed $default The default value to return if the language option is not configured. Default null
     * @return string|mixed the language option value if defined, the value in $default otherwise
     */
    public function optionLanguage($default = null)
    {
        $value = $this->options()->option(self::OPTION_LANGUAGE)->first();

        if(is_null($value)){
            return $default;
        }

        return $value->value;   
    }

}