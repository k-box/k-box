<?php

namespace Klink\DmsAdapter;

use InvalidArgumentException;

/**
* Utility functions to handle specific operations on documents given the file path
*/
class KlinkDocumentUtils
{
	
	private static $mimeTypesToDocType = array(

		'post' => 'web-page',
		'page' => 'web-page',
		'node' => 'web-page',
		'text/html' => 'web-page',
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

		);
		
	/**
	 * Array of mime types that are fully understood by the K-Link Core
	 */
	private static $indexableMimeTypes = array(

		'application/msword',
		'application/vnd.ms-excel',
		'application/vnd.ms-powerpoint',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/pdf',
		);


	private static $fileExtensionToMimeType = array(
		// Image formats.
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'bmp' => 'image/bmp',
		'tif|tiff' => 'image/tiff',
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
		// Google Earth files (aka Keyhole Markup Language)
		'kml' => 'application/vnd.google-earth.kml+xml',
		'kmz' => 'application/vnd.google-earth.kmz',
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
        'xml|gml|gpx' => 'text/xml', // Plain XML, gml: Geographic Markup Language - v2 (GML2), gpx: GPS Exchange Format (GPX)
        'shp|shx|sbn|dbf|aib|e02|e01|ovr|mxd|prt|jgw|tfw' => 'application/octet-stream', // Esri ArcGIS files
		) ;




	/**
	 * Computes the hash for uniquely identify the file
	 * Uses SHA-512 variant of SHA-2 (Secure hash Algorithm)
	 * @param string $filePath The file path
	 * @return string
	 */
	public static function generateDocumentHash( $filePath )
	{

		if(function_exists('mb_detect_encoding') && mb_detect_encoding($filePath) !== 'UTF-8'){
			$filePath = utf8_encode($filePath);
		}

		return hash_file( 'sha512', $filePath );

	}

	/**
	 * Computes the SHA-512 hash for the specified content
	 * @param string $content 
	 * @return string
	 */
	public static function generateHash( $content ){

		return hash( 'sha512', $content );

	}


	public static function areDocumentsTheSame($fileOne, $fileTwo){

		return self::generateDocumentHash( $fileOne ) === self::generateDocumentHash( $fileTwo );

	}

	/**
	 * Convert the mime type to a document type
	 * @param string $mimeType 
	 * @return string the correspondent 
	 */
	public static function documentTypeFromMimeType( $mimeType ){

		if(str_contains($mimeType, ';')){
			$mimeType = substr($mimeType, 0, strpos($mimeType, ';'));
		}

		if( array_key_exists($mimeType, self::$mimeTypesToDocType) ) {
			return self::$mimeTypesToDocType[$mimeType];
		}

		return "document";
	}

	/**
	 * Check if the specified mime type is one of the known mimetypes
	 * 
	 * @param string $mimeType the mime type to check for
	 * @return boolean true if known, false otherwise
	 */
	public static function isMimeTypeSupported( $mimeType ){

		return @array_key_exists($mimeType, self::$mimeTypesToDocType);

	}
	
	
	/**
	 * Check if the specified mime type is one of the supported mimetypes for indexing by the Core.
	 * 
	 * @param string $mimeType the mime type to check for
	 * @return boolean true if supported, false otherwise
	 */
	public static function isMimeTypeIndexable( $mimeType ){

		return @array_key_exists($mimeType, self::$mimeTypesToDocType) && in_array($mimeType, self::$indexableMimeTypes);

	}

	public static function getDocumentTypes(){
		return @array_values(self::$mimeTypesToDocType);
	}

	/**
	 * Return the file extension that corresponds to the given mime type
	 * @param  string $mimeType the mime-type of the file
	 * @return string           the known file extension
	 * @throws InvalidArgumentException If the mime type is unkwnown, null or empty
	 */
	public static function getExtensionFromMimeType( $mimeType ){

		$inverted = array_flip( self::$fileExtensionToMimeType );
		
		$comma = strpos($mimeType, ';');

		if($comma){
			$mimeType = substr($mimeType, 0, $comma);
		}

		$key = array_key_exists($mimeType, $inverted);

		if( $key ){

			$ext = $inverted[$mimeType];

			$pos = strpos($ext, '|');
			if( $pos !== false ){
				$ext = substr( $ext, 0, $pos);
			}

			return $ext;
		}

		throw new InvalidArgumentException("Unknown mime type.");

	}

	/**
	 * Gets the inferred mime type using the file extension
	 * @param  string $extension The file extension
	 * @return string            The mime type. returns `application/octet-stream` if the mime type is not known 
	 * @throws InvalidArgumentException if $extension is null or empty.
	 */
	public static function getMimeTypeFromExtension( $extension ){

		foreach ( self::$fileExtensionToMimeType as $exts => $mime ) {
                if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
                        return $mime;
                }
        }

        return 'application/octet-stream';

	}

	/**
	 * Get the mime type of the specified file
	 * 
	 * @param string $file the path of the file to get the mime type
	 * @return string|boolean the mime type or false in case of error
	 * @throws InvalidArgumentException if $file is empty or null
	 */
	public static function get_mime($file) {

		// we don't rely anymore to finfo_file function because for some docx created from LibreOffice the
		// mime type reported is Composite Document File V2 Document, which has totally no-sense

        $extension = pathinfo( $file, PATHINFO_EXTENSION );

        if ( !empty( $extension ) ) {

            return self::getMimeTypeFromExtension( $extension );

        }
        
        return 'application/octet-stream';

	}

	/**
	 * Encode a file, a string or a stream to base64
	 *
	 * @parameter string|resource $value the string to convert, the absolute file path or a stream (like the one coming from `fopen`)
	 * @return resource The stream containing the base64 encode of $value. Remember to close the stream once you have done.
	 * @throws UnexpectedValueException if $value is a closed stream 
	 */
	public static function getBase64Stream( $value ){
		
		if( is_resource($value) && @get_resource_type($value) === 'stream' ){
			rewind($value);
			
			$fp = tmpfile();
			stream_filter_append($fp, 'convert.base64-encode', STREAM_FILTER_WRITE);
			stream_copy_to_stream($value, $fp);
			
			rewind($fp);
			
			return $fp;
		}
		else if( @get_resource_type($value) === 'Unknown' ){
			throw new \UnexpectedValueException('The original document stream is closed');
		}
		
		if(@is_file($value)){

			$fp = tmpfile();
			
			$src = fopen($value, 'r');

			stream_filter_append($fp, 'convert.base64-encode', STREAM_FILTER_WRITE);
			stream_copy_to_stream($src, $fp);
			rewind($fp);

			fclose($src);

			return $fp;
			
		}
        
        $fp = tmpfile();

		stream_filter_append($fp, 'convert.base64-encode', STREAM_FILTER_WRITE);
		fwrite($fp, $value);
		rewind($fp);

        return $fp;
		
	}

}