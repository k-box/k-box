<?php

namespace KBox\Documents;

/**
 * The result of a type identifier.
 * Contains the mime type and document type identified for a given file
 */
final class TypeIdentification
{
    /**
     * The identified mime type
     *
     * @var string
     */
    public $mimeType = null;

    /**
     * The identified document type.
     * Must be one of the {@see KBox\Documents\DocumentType} defined
     *
     * @var string
     */
    public $documentType = null;

    public function __construct($mimeType = null, $documentType = null)
    {
        $this->mimeType = $mimeType;
        $this->documentType = $documentType;
    }

    public function create($mimeType, $documentType)
    {
        return new self($mimeType, $documentType);
    }

    public function toArray()
    {
        return [$this->mimeType, $this->documentType];
    }

    public function __toString()
    {
        return "$this->mimeType.$this->documentType";
    }
}
