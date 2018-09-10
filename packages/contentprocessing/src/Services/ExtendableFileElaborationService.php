<?php

namespace KBox\Documents\Services;

use KBox\File;
use KBox\Documents\Exceptions\InvalidDriverException;
use KBox\Documents\Exceptions\DriverNotFoundException;
use KBox\Documents\Exceptions\UnsupportedFileException;

class ExtendableFileElaborationService
{

    /**
     * The default drivers
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Supported file mime types.
     *
     * The list is generated from the configured entries
     *
     * @var array|null
     */
    private $supportedMimeTypes = null;

    /**
     * Return the list of configured drivers
     *
     * @return array<string>
     */
    public function drivers()
    {
        return $this->drivers;
    }

    /**
     * Register a driver
     *
     * @param string $driver the driver class to register
     * @return self
     */
    public function register(string $driver)
    {
        if (in_array($driver, $this->drivers)) {
            return $this;
        }

        $this->validateDriver($driver);

        array_push($this->drivers, $driver);
        
        // clean the cache of supported mime types since there
        // was a change in the drivers list
        $this->supportedMimeTypes = null;
        
        return $this;
    }

    /**
     * Validate the driver class
     */
    protected function validateDriver($driverClass)
    {
        if (! class_exists($driverClass)) {
            throw InvalidDriverException::classNotExists($driverClass);
        }
    }

    protected function driverFor(File $file)
    {
        if (! $this->isSupported($file)) {
            throw UnsupportedFileException::file($file);
        }

        // get the first driver that support the file
        $driver = collect($this->drivers)->first(function ($driver) use ($file) {
            return (new $driver())->isSupported($file);
        });

        if (is_null($driver)) {
            throw DriverNotFoundException::for($file);
        }

        return new $driver();
    }

    /**
     * Return the list of supported mime type
     *
     * @return array<string>
     */
    public function supportedMimeTypes()
    {
        if (! is_null($this->supportedMimeTypes)) {
            return $this->supportedMimeTypes;
        }

        $mimeTypes = collect($this->drivers)->map(function ($driver) {
            return (new $driver())->supportedMimeTypes();
        })->flatten()->toArray();

        return $this->supportedMimeTypes = $mimeTypes;
    }

    /**
     * Check if a given File is supported by the configured entries.
     * The check is performed at mime type level
     *
     * @param File $file The {@see File} to check
     * @return bool
     */
    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes());
    }
}
