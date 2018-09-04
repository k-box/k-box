<?php

namespace KBox\Documents;

/**
 * The default type identifier to extend to create your own type identifiers
 */
abstract class TypeIdentifier
{
    /**
     * The accepted files.
     *
     * Specify an array of mime types to get files matching
     * that mime type or * to match all files
     *
     * @var string|array
     */
    public $accept = "*";

    /**
     * The priority of this identifier.
     * Use positive integers, starting from 1, which is the lowest priority
     *
     * @var int
     */
    public $priority = 1;

    /**
     * Identify a file mime and document type
     *
     * @param string $path The path of the file to analyze
     * @param TypeIdentification $default The type identified by the default identifier
     * @return TypeIdentification the identified mime and document type
     */
    abstract public function identify(string $path, TypeIdentification $default) : TypeIdentification;
}
