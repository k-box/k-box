<?php

namespace Content\Contracts;

/**
 * FileLoader interface.
 * 
 * Define what methods must be exposed by class that is able to 
 * load a file from the filesystem.
 */
interface FileLoader
{

    /**
     * Load the file from filesystem
     *
     * @param string the path of the file to load
     * @return FileLoader
     */
    public function load($path);

    /**
     * The file properties
     *
     * @return \Content\FileProperties
     */
    public function properties();

    /**
     * The list of supported mime types
     *
     * @return array
     */
    public function supportedMimeTypes();

}