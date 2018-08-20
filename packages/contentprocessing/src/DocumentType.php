<?php

namespace KBox\Documents;

use KBox\Traits\HasEnums;
use InvalidArgumentException;

/**
 * The document types.
 * 
 * Define the list of document types for files added to the K-Box
 */
class DocumentType
{

    use HasEnums;

    /**
     * A generic document
     */
    const DOCUMENT = 'document';
    
    /**
     * A word processing document, created for example with Microsoft(tm) Word(tm)
     */
    const WORD_DOCUMENT = 'word-document';
    
    /**
     * A PDF document
     */
    const PDF_DOCUMENT = 'pdf-document';

    /**
     * A generic web page
     */
    const WEB_PAGE = 'web-page';
    
    const SPREADSHEET = 'spreadsheet';
    
    const PRESENTATION = 'presentation';
    
    /**
     * A generic plain text file
     */
    const TEXT = 'text';
    
    /**
     * A file that contains code, like a cpp source file
     */
    const CODE = 'code';

    /**
     * A generic binary file
     */
    const BINARY = 'binary';

    /**
     * A list of URIs
     */
    const URI_LIST = 'uri-list';
    
    const IMAGE = 'image';
    
    const VIDEO = 'video';
    
    const ARCHIVE = 'archive';
    
    const EMAIL = 'email';

    /**
     * Geographic data. A file that contains georeferenced data
     */
    const GEODATA = 'geodata';

    const DVD_VIDEO = 'dvd-video';
    
    const NOTE = 'note';
    
    const CALENDAR = 'calendar';

    private static $mimeTypesToDocType = [
        'text/html' => self::WEB_PAGE,
        'application/msword' => 'document',
        'application/vnd.ms-excel' => 'spreadsheet',
        'application/vnd.ms-powerpoint' => 'presentation',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'spreadsheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'presentation',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'document',
        'application/pdf' => 'document',
        'text/uri-list' => 'uri-list',
        'image/jpg' => 'image',
        'image/jpeg' => 'image',
        'image/gif' => 'image',
        'image/png' => 'image',
        'image/tiff' => 'image',
        'text/plain' => 'text-document',
        'application/rtf' => 'text-document',
        'text/x-markdown' => 'text-document',
        'application/vnd.google-apps.document' => 'document',
        'application/vnd.google-apps.drawing' => 'image',
        'application/vnd.google-apps.form' => 'form',
        'application/vnd.google-apps.fusiontable' => 'spreadsheet',
        'application/vnd.google-apps.presentation' => 'presentation',
        'application/vnd.google-apps.spreadsheet' => 'spreadsheet',
        'application/vnd.google-earth.kml+xml' => 'geodata',
        'application/vnd.google-earth.kmz' => 'geodata',
        'application/rar' => 'archive',
        'application/zip' => 'archive',
        'application/x-tar' => 'archive',
        'application/x-bzip2' => 'archive',
        'application/gzip' => 'archive',
        'application/x-gzip' => 'archive',
        'application/x-mimearchive' => 'web-page',
        'video/x-ms-vob' => 'dvd',
        'content/DVD' => 'dvd',
        'video/x-ms-wmv' => 'video',
        'video/x-ms-wmx' => 'video',
        'video/x-ms-wm' => 'video',
        'video/avi' => 'video',
        'video/divx' => 'video',
        'video/x-flv' => 'video',
        'video/quicktime' => 'video',
        'video/mpeg' => 'video',
        'video/mp4' => 'video',
        'video/ogg' => 'video',
        'video/webm' => 'video',
        'video/x-matroska' => 'video',
        'video/3gpp' => 'video',
        'video/3gpp2' => 'video',
        'text/csv' => 'spreadsheet',
        'message/rfc822' => 'email',
        'application/vnd.ms-outlook' => 'email',
        'application/gpx+xml' => 'geodata',
        'application/geo+json' => 'geodata',
    ];

    /**
     * Convert the mime type to a document type
     * @param string $mimeType
     * @return string the correspondent
     */
    public static function documentTypeFromMimeType($mimeType)
    {
        if (str_contains($mimeType, ';')) {
            $mimeType = str_before($mimeType, ';');
        }

        if (array_key_exists($mimeType, self::$mimeTypesToDocType)) {
            return self::$mimeTypesToDocType[$mimeType];
        }

        return self::BINARY;
    }

    /**
     * Get the mime type of the specified file
     *
     * @param string $file the path of the file to get the mime type
     * @return string|boolean the mime type or false in case of error
     * @throws InvalidArgumentException if $file is empty or null
     */
    public static function get_mime($file)
    {

        // we don't rely anymore to finfo_file function because for some docx created from LibreOffice the
        // mime type reported is Composite Document File V2 Document, which has totally no-sense

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (! empty($extension)) {
            return self::getMimeTypeFromExtension($extension);
        }
        
        return 'application/octet-stream';
    }

}
