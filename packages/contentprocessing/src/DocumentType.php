<?php

namespace KBox\Documents;

use KBox\Traits\HasEnums;

/**
 * The document types.
 *
 * Define the list of document types for files added to the K-Box
 */
final class DocumentType
{
    use HasEnums;

    /**
     * A generic document. Sometimes used also for Word, PDF and unknown types
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
     * An html file
     */
    const WEB_PAGE = 'web-page';
    
    /**
     * A spreadsheet
     */
    const SPREADSHEET = 'spreadsheet';
    
    /**
     * A presentation, made for example with Power Point
     */
    const PRESENTATION = 'presentation';
    
    /**
     * A generic plain text file
     */
    const TEXT_DOCUMENT = 'text-document';
    
    /**
     * A file that contains code, like a cpp source file
     */
    const CODE = 'code';

    /**
     * A generic binary file, can be an executable or an unknown file in binary format
     */
    const BINARY = 'binary';

    /**
     * A uri file that contains a list of URLs/URIs
     */
    const URI_LIST = 'uri-list';
    
    /**
     * Image file
     */
    const IMAGE = 'image';
    
    /**
     * Video file
     */
    const VIDEO = 'video';
    
    /**
     * A Video that is a DVD file
     */
    const DVD_VIDEO = 'dvd-video';

    /**
     * A compressed file, for example a zip or tar file
     */
    const ARCHIVE = 'archive';
    
    /**
     * The file is a saved email
     */
    const EMAIL = 'email';

    /**
     * Geographic data. A file that contains geographical referenced data
     */
    const GEODATA = 'geodata';

    /**
     * The file comes from a note taking application, like OneNote
     */
    const NOTE = 'note';
    
    /**
     * The file is a calendar link, maybe ICS
     */
    const CALENDAR = 'calendar';
    
    /**
     * A series of questions or fields
     */
    const FORM = 'form';

    public static $mimeTypesToDocType = [
        'application/vnd.google-apps.document' => self::DOCUMENT,
        
        'application/pdf' => self::PDF_DOCUMENT,
        
        'application/msword' => self::WORD_DOCUMENT,
        'application/vnd.apple.pages' => self::WORD_DOCUMENT,
        'application/vnd.oasis.opendocument.text' => self::WORD_DOCUMENT,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => self::WORD_DOCUMENT,
        
        'text/plain' => self::TEXT_DOCUMENT,
        'application/rtf' => self::TEXT_DOCUMENT,
        'text/x-markdown' => self::TEXT_DOCUMENT,
        
        'application/vnd.ms-powerpoint' => self::PRESENTATION,
        'application/vnd.apple.keynote' => self::PRESENTATION,
        'application/vnd.google-apps.presentation' => self::PRESENTATION,
        'application/vnd.oasis.opendocument.presentation' => self::PRESENTATION,
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => self::PRESENTATION,

        'text/csv' => self::SPREADSHEET,
        'application/vnd.ms-excel' => self::SPREADSHEET,
        'application/vnd.apple.numbers' => self::SPREADSHEET,
        'application/vnd.google-apps.spreadsheet' => self::SPREADSHEET,
        'application/vnd.google-apps.fusiontable' => self::SPREADSHEET,
        'application/vnd.oasis.opendocument.spreadsheet' => self::SPREADSHEET,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => self::SPREADSHEET,
        
        'text/html' => self::WEB_PAGE,
        'application/x-mimearchive' => self::WEB_PAGE,
        
        'text/uri-list' => self::URI_LIST,
        
        'image/jpg' => self::IMAGE,
        'image/jpeg' => self::IMAGE,
        'image/gif' => self::IMAGE,
        'image/png' => self::IMAGE,
        'image/tiff' => self::IMAGE,
        'image/svg+xml' => self::IMAGE,
        'image/vnd.adobe.photoshop' => self::IMAGE,
        'application/vnd.google-apps.drawing' => self::IMAGE,
        'application/vnd.oasis.opendocument.graphics' => self::IMAGE,

        'application/rar' => self::ARCHIVE,
        'application/zip' => self::ARCHIVE,
        'application/gzip' => self::ARCHIVE,
        'application/x-tar' => self::ARCHIVE,
        'application/x-gzip' => self::ARCHIVE,
        'application/x-bzip2' => self::ARCHIVE,
        'application/x-7z-compressed' => self::ARCHIVE,
        
        'content/DVD' => self::DVD_VIDEO,
        'video/x-ms-vob' => self::DVD_VIDEO,
        
        'video/avi' => self::VIDEO,
        'video/mp4' => self::VIDEO,
        'video/ogg' => self::VIDEO,
        'video/divx' => self::VIDEO,
        'video/mpeg' => self::VIDEO,
        'video/webm' => self::VIDEO,
        'video/3gpp' => self::VIDEO,
        'video/x-flv' => self::VIDEO,
        'video/3gpp2' => self::VIDEO,
        'video/x-ms-wm' => self::VIDEO,
        'video/x-ms-wmv' => self::VIDEO,
        'video/x-ms-wmx' => self::VIDEO,
        'video/quicktime' => self::VIDEO,
        'video/x-matroska' => self::VIDEO,
        
        'message/rfc822' => self::EMAIL,
        'application/vnd.ms-outlook' => self::EMAIL,
        
        'application/gpx+xml' => self::GEODATA,
        'application/geo+json' => self::GEODATA,
        'application/vnd.google-earth.kmz' => self::GEODATA,
        'application/vnd.google-earth.kml+xml' => self::GEODATA,
        
        'text/calendar' => self::CALENDAR,
        
        'application/onenote' => self::NOTE,
        
        'application/java' => self::CODE,
        'application/json' => self::CODE,
        'application/javascript' => self::CODE,
        
        'application/vnd.google-apps.form' => self::FORM,

        'application/octet-stream' => self::BINARY,
        'application/octet-stream' => self::BINARY,
    ];
    
    /**
     * Convert the mime type to a document type
     * @param string $mimeType
     * @return string the correspondent document type, or self::BINARY if unknown
     * @deprecated use from() instead
     */
    public static function documentTypeFromMimeType($mimeType)
    {
        return self::from($mimeType);
    }
    
    /**
     * Get the corresponding document type of a mime type
     *
     * @param string $mimeType
     * @return string the correspondent document type, or self::BINARY if unknown
     * @see self::documentTypeFromMimeType()
     */
    public static function from($mimeType)
    {
        if (str_contains($mimeType, ';')) {
            $mimeType = str_before($mimeType, ';');
        }

        if (array_key_exists($mimeType, self::$mimeTypesToDocType)) {
            return self::$mimeTypesToDocType[$mimeType];
        }

        return self::BINARY;
    }
}
