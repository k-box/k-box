<?php

namespace KBox\Documents;

use Exception;
use InvalidArgumentException;

/**
 * Utility functions for working with files on disk.
 *
 * Please interact with the FileService instead of using the file helper directly, as
 * the FileService provides abstraction and extension support
 */
final class FileHelper
{

    /**
     * The default mime type, if not recognized
     *
     * @var string
     */
    const DEFAULT_MIME_TYPE = 'application/octet-stream';

    /**
     * The default document type, if not recognized
     *
     * @var string
     */
    const DEFAULT_DOCUMENT_TYPE = DocumentType::BINARY;

    /**
     * File extensions to mime type map
     *
     * @var array
     */
    private static $fileExtensionToMimeType = [
        // Image formats.
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'tiff|tif' => 'image/tiff',
        'ico' => 'image/x-icon',
        // Video formats.
        'asf|asx' => 'video/x-ms-asf',
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wm' => 'video/x-ms-wm',
        'avi' => 'video/avi',
        'vob' => 'video/x-ms-vob',
        'ifo|bup' => 'content/DVD', // DVD video information file
        'divx' => 'video/divx',
        'flv' => 'video/x-flv',
        'mov|qt' => 'video/quicktime',
        'mpeg|mpg|mpe' => 'video/mpeg',
        'mp4|m4v' => 'video/mp4',
        'ogv' => 'video/ogg',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',
        '3gp|3gpp' => 'video/3gpp', // Can also be audio
        '3g2|3gp2' => 'video/3gpp2', // Can also be audio
        // Text formats.
        'txt|asc|c|cc|h|srt|wkt' => 'text/plain', // WKT stands for Well-Known-Text
        'csv' => 'text/csv',
        'tsv' => 'text/tab-separated-values',
        'ics' => 'text/calendar',
        'rtx' => 'text/richtext',
        'css' => 'text/css',
        'html|htm' => 'text/html',
        'mhtml|mht' => 'application/x-mimearchive',
        'vtt' => 'text/vtt',
        'dfxp' => 'application/ttaf+xml',
        // Audio formats.
        'mp3|m4a|m4b' => 'audio/mpeg',
        'ra|ram' => 'audio/x-realaudio',
        'wav' => 'audio/wav',
        'ogg|oga' => 'audio/ogg',
        'mid|midi' => 'audio/midi',
        'wma' => 'audio/x-ms-wma',
        'wax' => 'audio/x-ms-wax',
        'mka' => 'audio/x-matroska',
        // Misc application formats.
        'rtf' => 'application/rtf',
        'md|markdown' => 'text/x-markdown',
        'js' => 'application/javascript',
        'pdf' => 'application/pdf',
        'swf' => 'application/x-shockwave-flash',
        'class' => 'application/java',
        'tar' => 'application/x-tar',
        'zip' => 'application/zip',
        'gz|gzip' => 'application/gzip', // As of RFC 6713 has an official mime type not prefixed with "x" https://tools.ietf.org/html/rfc6713
        'bz2' => 'application/x-bzip2',
        'rar' => 'application/rar',
        '7z' => 'application/x-7z-compressed',
        'exe' => 'application/x-msdownload',
        // MS Office formats.
        'doc' => 'application/msword',
        'ppt|pps|pot' => 'application/vnd.ms-powerpoint',
        'wri' => 'application/vnd.ms-write',
        'xls|xla|xlt|xlw' => 'application/vnd.ms-excel',
        'mdb' => 'application/vnd.ms-access',
        'mpp' => 'application/vnd.ms-project',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
        'oxps' => 'application/oxps',
        'xps' => 'application/vnd.ms-xpsdocument',
        // OpenOffice formats.
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        // WordPerfect formats.
        'wp|wpd' => 'application/wordperfect',
        // iWork formats.
        'key' => 'application/vnd.apple.keynote',
        'numbers' => 'application/vnd.apple.numbers',
        'pages' => 'application/vnd.apple.pages',
        'uri' => 'text/uri-list',
        // Google Docs formats.
        'gdoc' => 'application/vnd.google-apps.document',
        'gdraw' => 'application/vnd.google-apps.drawing',
        'gform' => 'application/vnd.google-apps.form',
        'gtable' => 'application/vnd.google-apps.fusiontable',
        'gslides' => 'application/vnd.google-apps.presentation',
        'gsheet' => 'application/vnd.google-apps.spreadsheet',
        // Mail messages
        'eml' => 'message/rfc822', // textual email message
        'msg' => 'application/vnd.ms-outlook', // Outlook Email Message
        // Adobe
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'indd' => 'application/x-indesign',
        // Access related
        'mdb|ldb' => 'application/x-msaccess',
        // Other image formats
        'pjpg' => 'image/pjpeg',
        'svg' => 'image/svg+xml',
        // Other
        'mxf' => 'application/mxf',
        'json' => 'application/json',
        'xml|gml' => 'text/xml', // Plain XML, gml: Geographic Markup Language - v2 (GML2), gpx:
        'bin' => 'application/octet-stream', // Plain Binary File
        '' => 'application/octet-stream', // No Extension => Plain Binary File
        // Geographical data
        'shp|shx|sbn|dbf|aib|e02|e01|ovr|mxd|prt|jgw|tfw' => 'application/octet-stream', // Esri ArcGIS files
        'gpx' => 'application/gpx+xml', // GPS Exchange Format (GPX)
        'geojson' => 'application/geo+json',
        'geotiff' => [DocumentType::GEODATA => 'image/tiff'],
        'kml' => 'application/vnd.google-earth.kml+xml', // Google Earth files (aka Keyhole Markup Language)
        'kmz' => 'application/vnd.google-earth.kmz',
    ];

    /**
     * Sometimes mime types are recognized differently according to the platform.
     * This associative array tries to reduce the known variations of mime types
     *
     * @var array
     */
    private static $normalizableMimeTypes = [
        'application/x-zip-compressed' => 'application/zip',
    ];
    
    /**
     * Mime types recognized by the underlying OS that should be discarded.
     * This list is compiled based on previous evaluation and
     * user submitted bug reports
     *
     * @var array
     */
    private static $discardMimeTypes = [
        'application/CDFV2-corrupt',
        'application/CDFV2-encrypted',
        'application/CDFV2',
    ];
    
    /**
     * Computes the hash of the file content
     *
     * Uses SHA-512 variant of SHA-2 (Secure hash Algorithm)
     *
     * @param string $path The file path
     * @return string
     */
    public static function hash(string $path)
    {
        if (function_exists('mb_detect_encoding') && mb_detect_encoding($path) !== 'UTF-8') {
            $path = utf8_encode($path);
        }

        return hash_file('sha512', $path);
    }

    /**
     * Retrieve the mime type and document type of a file, given its path on disk
     *
     * @param string $path The path to the file
     * @return array with mime type, as first element, and document type, as second
     */
    public static function type($path)
    {
        try {
            $mime = self::get_mime($path);
            $doc = DocumentType::from($mime);

            return [$mime, $doc];
        } catch (Exception $ex) {
        }

        return [static::DEFAULT_MIME_TYPE, static::DEFAULT_DOCUMENT_TYPE];
    }

    /**
     * Check if the specified mime type is one of the known mimetypes
     *
     * @param string $mimeType the mime type to check for
     * @return boolean true if known, false otherwise
     */
    public static function isMimeTypeSupported($mimeType)
    {
        return @array_key_exists(self::normalizeMimeType($mimeType), array_flip(self::$fileExtensionToMimeType));
    }
    
    /**
     * Return the file extension that corresponds to the given mime type and document type
     *
     * @param  string $mimeType the mime-type of the file
     * @param  string $documentType the document-type of the file. Default null
     * @return string           the known file extension
     * @throws InvalidArgumentException If the mime type is unkwnown, null or empty
     */
    public static function getExtensionFromType($mimeType, $documentType = null)
    {
        $mimeType = self::normalizeMimeType(str_before($mimeType, ';'));
   
        $possible_extensions = array_filter(self::$fileExtensionToMimeType, function ($value, $key) use ($mimeType, $documentType) {
            if (is_array($value) && ! is_null($documentType) &&  array_key_exists($documentType, $value)) {
                return $value[$documentType] === $mimeType;
            }

            return $value === $mimeType;
        }, ARRAY_FILTER_USE_BOTH);

        $possible_extensions_count = count($possible_extensions);

        if ($possible_extensions_count > 0) {
            $possible_extension = array_first(array_keys($possible_extensions));
            
            if (! is_null($documentType) && $possible_extensions_count > 1) {
                $filtered_by_doc_type = array_keys(array_filter($possible_extensions, function ($value, $key) use ($mimeType, $documentType) {
                    if (is_array($value) && ! is_null($documentType) &&  array_key_exists($documentType, $value)) {
                        return $value[$documentType] === $mimeType;
                    }
                    
                    return false;
                }, ARRAY_FILTER_USE_BOTH));

                if (count($filtered_by_doc_type) > 0) {
                    $possible_extension = array_first($filtered_by_doc_type);
                }
            }
            
            return str_before($possible_extension, '|');
        }

        throw new InvalidArgumentException("Unknown mime type.");
    }

    /**
     * Gets the inferred mime type using the file extension
     * @param  string $extension The file extension
     * @return string            The mime type. returns `application/octet-stream` if the mime type is not known
     * @throws InvalidArgumentException if $extension is null or empty.
     */
    public static function getMimeTypeFromExtension($extension)
    {
        if (empty($extension)) {
            throw new InvalidArgumentException("Extension must be a non-empty string");
        }

        foreach (self::$fileExtensionToMimeType as $exts => $mime) {
            if (preg_match('!^('.$exts.')$!i', $extension)) {
                return $mime;
            }
        }

        return self::DEFAULT_MIME_TYPE;
    }

    /**
     * Get the mime type of the specified file
     *
     * @param string $file the path of the file to get the mime type
     * @return string the mime type or false in case of error
     * @throws InvalidArgumentException if $file is empty or null
     */
    public static function get_mime($file)
    {
        // Get the mime type from the OS
        $fileinfoMimeType = null;
        try {
            $fileinfoMimeType = self::normalizeMimeType(mime_content_type($file));
    
            if (self::isMimeTypeToDiscard($fileinfoMimeType)) {
                $fileinfoMimeType = null;
            }
        } catch (Exception $ex) {
        }

        // Get the mime type based on the file extension
        $extensionMimeType = null;
        try {
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if (! empty($extension)) {
                $extensionMimeType = self::getMimeTypeFromExtension($extension);
            }
        } catch (InvalidArgumentException $ex) {
        }

        // Verify if the two methods recognized the same
        // as in the past some docx created from LibreOffice were
        // reported as Composite Document File V2 Document from the OS
        
        if (is_null($fileinfoMimeType) && is_null($extensionMimeType)) {
            return self::DEFAULT_MIME_TYPE;
        }
        
        if (is_null($fileinfoMimeType) && ! is_null($extensionMimeType)) {
            return $extensionMimeType;
        }
        
        if (! is_null($fileinfoMimeType) && is_null($extensionMimeType)) {
            return $fileinfoMimeType;
        }
        
        if ($fileinfoMimeType !== $extensionMimeType) {
            return $extensionMimeType;
        }

        return $fileinfoMimeType;
    }

    /**
     * Sometimes different platforms return non standard mime types.
     * This function tries to migrate non standard mime types to
     * supported ones
     *
     * @param string $mimeType
     * @return string the normalized mime type
     */
    public static function normalizeMimeType($mimeType)
    {
        return self::$normalizableMimeTypes[$mimeType] ?? $mimeType;
    }

    /**
     * Check if the mime type needs to be discarded according to the discard policy
     *
     * @param string $mimeType
     * @return bool
     */
    private static function isMimeTypeToDiscard($mimeType)
    {
        return in_array($mimeType, self::$discardMimeTypes);
    }
}
