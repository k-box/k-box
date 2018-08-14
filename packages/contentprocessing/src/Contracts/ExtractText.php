<?php

namespace KBox\Documents\Contracts;

/**
 * ExtractText interface.
 *
 * Define what methods must be exposed by a text extractor class.
 * A text extractor class converts the content of a specific file type to plain text
 */
interface ExtractText extends FileLoader
{

    /**
     * Extract the plain text file content
     *
     * @return string|stream the plain text file content
     */
    public function text();
}
