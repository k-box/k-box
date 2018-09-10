<?php

namespace KBox\Documents\Exceptions;

use Exception;

/**
 * If a driver is not compatible with the service
 */
class InvalidDriverException extends Exception
{

    /**
     * Create a new exception
     *
     * @param string $message
     *
     * @param Exception $previous
     * @return InvalidDriverException
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 80001, $previous);
    }

    /**
     * Create a new InvalidDriverException for non existing driver class
     *
     * @param string $driverClass
     * @return InvalidDriverException
     */
    public static function classNotExists(string $driverClass)
    {
        return new self("The specified driver class [{$driverClass}] does not exists or cannot be loaded from file.");
    }

    /**
     * Create a new InvalidDriverException for non existing driver class
     *
     * @param string $driverClass
     * @param string $interfaceToImplement
     * @return InvalidDriverException
     */
    public static function classNotImplements(string $driverClass, string $interfaceToImplement)
    {
        return new self("The specified driver class [{$driverClass}] does implement [$interfaceToImplement].");
    }
}
