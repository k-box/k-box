<?php

namespace KBox\Documents\Services;

use KBox\Documents\FileHelper;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Preview\PreviewFactory;

/**
 * Service to interact with physical file for preview, 
 * text extraction, thumbnail generation and 
 * mime type/document type recognition
 */
class FileService
{

    // public function register($mimeType, $documentType, $extension, $previewGenerator, $thumbnailGenerator, $textExtractor)
    // {
    //     // probably here I would register the resolver from plugins    
    // }


    /**
     * Computes the hash of the file content
     * 
     * Uses SHA-512 variant of SHA-2 (Secure hash Algorithm)
     * 
     * @param string $path The file path
     * @return string
     */
    public function hash(string $path)
    {
        $absolute_path = @is_file($path) ? $path : Storage::path($path);

        return FileHelper::hash($absolute_path);
    }


    /**
     * Retrieve the mime type and document type of a file, given its path on disk
     * 
     * @param string $path The path to the file
     * @return array with mime type as first element, and document type as second
     */
    public function recognize($path)
    {
        $absolute_path = @is_file($path) ? $path : Storage::path($path);

        return FileHelper::type($absolute_path);
    }

    /**
     * Return the file extension that corresponds to the given mime type and document type
     * 
     * @param  string $mimeType the mime-type of the file
     * @param  string $documentType the document-type of the file. Default null
     * @return string           the known file extension
     * @throws InvalidArgumentException If the mime type is unkwnown, null or empty
     */
    public static function extensionFromType($mimeType, $documentType = null)
    {
        return FileHelper::getExtensionFromType($mimeType, $documentType);
    }

}
