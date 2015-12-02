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
     */
    public function optionLanguage($default = null){

        $value = $this->options()->option(self::OPTION_LANGUAGE)->first();

        if(is_null($value)){
            return $default;
        }

        return $value->value;   
    }

}