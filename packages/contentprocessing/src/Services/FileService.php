<?php

namespace KBox\Documents\Services;

use KBox\Documents\Preview\PreviewFactory;

/**
 * Service to interact with physical file for preview, 
 * text extraction, thumbnail generation and 
 * mime type/document type recognition
 */
class FileService
{

    
    public function register($mimeType, $documentType, $extension, $previewGenerator, $thumbnailGenerator, $textExtractor)
    {
        // qui registro il tipo di file    
    }
}
