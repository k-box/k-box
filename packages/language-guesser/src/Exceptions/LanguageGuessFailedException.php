<?php

namespace OneOffTech\LanguageGuesser\Exceptions;

use Exception;

class LanguageGuessFailedException extends Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
