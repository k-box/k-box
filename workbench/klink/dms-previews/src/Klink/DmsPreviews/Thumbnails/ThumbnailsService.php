<?php namespace Klink\DmsPreviews\Thumbnails;

use Illuminate\Support\ServiceProvider;

use KlinkDMS\File;

use Symfony\Component\Finder\Finder;

use KlinkDMS\Exceptions\ForbiddenException;
use KlinkDMS\Exceptions\FileNamingException;
use Illuminate\Support\Collection;

use Klink\DmsAdapter\Contracts\KlinkAdapter;
use \KlinkDocumentUtils;
use Log;
use Exception;


/**
 * The service responsible for the generation of the {@see File} 
 * thumbnail
 *
 * This service uses the thumbnail generation endpoint offered by 
 * the K-Core.
 */
class ThumbnailsService {

	const THUMBNAILS_FOLDER_NAME = 'thumbnails';
	const THUMBNAIL_IMAGE_FORMAT = 'image/png';
	const THUMBNAIL_IMAGE_EXTENSION = '.png';

	/**
	 * Supported file mime types.
	 * 
	 * If the file mime type is not here, a tentative, default thumbnail 
	 * is returned
	 */
	private static $supportedMime = array(
        'application/pdf', 
        'image/png', 
        'image/gif', 
        'image/jpg', 
        'image/jpeg',
        'text/html', // must be an external http/https url
        // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        // 'text/plain',
        // 'application/rtf',
        // 'text/x-markdown',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        );

	/**
	 * The adapter to use when the remote thumbnail API 
	 * service is needed
	 *
	 * @var \Klink\DmsAdapter\Contracts\KlinkAdapter
	 */
	private $adapter = null;

	/**
	 * Create a new ThumbnailsService instance.
	 *
	 * @param \KlinkAdapter $adapter The reference K-Link Core with the thumbnail service endpoint
	 * @return void
	 */
	public function __construct(KlinkAdapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * Generate a thumbnail for a given {@see File}
	 *
	 * The generated thumbnail is saved on disk and its path is 
	 * added to the file thumbnail_path attribute. If a file 
	 * already has a thumbnail path, that file will be returned.
	 *
	 * If the thumbnail cannot be generated a default thumbnail 
	 * for the specific document type is returned.
	 *
	 * @param File $file The {@see File} you want the thumbnail for.
	 * @param boolean $force Override the already generated thumbnail. Default false.
	 * @return string The path (on disk) of the thumbnail image.
	 * @throws Exception In case after the thumbnail generation its location is not a valid file
	 */
	public function generate(File $file, $force = false)
	{
		if(!is_null($file->thumbnail_path) && is_file($file->thumbnail_path) && !$force)
		{
			return $file->thumbnail_path;
		}
		
		// get file mime type
		$charset_pos = strpos($file->mime_type, ';'); 
		$mime = $charset_pos !== false ? trim(substr($file->mime_type, 0, $charset_pos)) : $file->mime_type; 

		Log::info("Processing thumbnail generation for file {$file->id} ({$mime})...");

		$default_path = $this->getDefaultThumbnail($mime);

		// check if the mimetype is included in the supported list

		if(!in_array($mime, self::$supportedMime))
		{
			Log::warning("File {$file->id} with mime type {$mime} not supported. Returning default thumbnail.");

			$file->thumbnail_path = $default_path;

			$file->save();

			return $default_path;
		}

		$thumb_save_path = $this->getSavePath($file);

		// check if html and remote website
		// and let the other mime types proceed
		$is_webpage = $file->isRemoteWebPage();
	
		try
		{
			if($is_webpage)
			{
				$thumb_save_path = $this->generateThumbnailForWebsite($file->original_uri, $thumb_save_path);
			}
			else 
			{
				$thumb_save_path = $this->generateThumbnailUsingRemoteService($mime, $file->path, $thumb_save_path);	
			}
		}
		catch(Exception $kex)
		{

			Log::error('Error generating thumbnail', array('param' => $file->toArray(), 'exception' => $kex));

			$thumb_save_path = $this->getDefaultThumbnail($mime);

		}
		catch(ErrorException $kex)
		{

			Log::error('Error generating thumbnail', array('param' => $file->toArray(), 'exception' => $kex));

			$thumb_save_path = $this->getDefaultThumbnail($mime);

		}

		if(!is_file($thumb_save_path))
		{
			Log::error("Thumbnail file $thumb_save_path is not a valid file.", array('param' => $file->toArray()));
			throw new Exception('Thumbnail not saved');
		}


		// if force is applied, delete the old thumbnail file from disk

		if($force && !is_null($file->thumbnail_path) && 
		   is_file($file->thumbnail_path) && strpos($file->thumbnail_path, 'public') === false)
		{
			unlink($file->thumbnail_path);
		}

		// saving back everything

		$file->thumbnail_path = $thumb_save_path;

		$file->save();

		return $thumb_save_path;
	}

	/**
	 * Generates the thumbnail of a website url.
	 * 
	 * @param  string $url the webpage url
	 * @param  string $savePath the absolute path, with filename, where to save the thumbnail
	 * @return string       The thumbnail path
	 */
	private function generateThumbnailForWebsite($url, $savePath)
	{

		$saved = $this->adapter->generateThumbnailOfWebSite($url, $savePath);

		return $savePath;

	}


	/**
	 * Generates the thumbnail using the Kcore thumbnail service.
	 * 
	 * @param  string $mime the file mimetype
	 * @param  string $filePath the file to generate the thumbnail for
	 * @param  string $savePath the absolute path, with filename, where to save the thumbnail
	 * @return string       The thumbnail path
	 */
	private function generateThumbnailUsingRemoteService($mime, $filePath, $savePath)
	{

		if($mime === 'image/jpg')
		{
			$mime = 'image/jpeg';
			// to overcome a problem in KlinkDocumentUtils::getExtensionFromMimeType that
			// is only able to associate the jpg extention to image/jpeg and not to image/jpg 
		}

		$fileContent = $this->adapter->generateThumbnailFromContent( $mime, $filePath );
		file_put_contents( $savePath, $fileContent );

		return $savePath;

	}

	/**
	 * Get the absolute path of the thumbnail file
	 *
	 * @param File $file the file to get the thumbnail path for
	 * @return string the location where to save the file thumbnail
	 */
	private function getSavePath(File $file)
	{
		$dir = dirname($file->path) . '/' . self::THUMBNAILS_FOLDER_NAME . '/';

		$is_dir = is_dir($dir);

		if(!$is_dir)
		{
			// create containing folder
			$is_dir = mkdir($dir, 0755, true);

			if(!$is_dir)
			{
				Log::error("Cannot create thumbnail folder $dir");

				$dir = dirname($file->path) . '/';
			}
		}

		return $dir . substr($file->hash, 0, 42) . self::THUMBNAIL_IMAGE_EXTENSION;
	}

	/**
	 * Get the default thumbnail associated to a mime type
	 *
	 * @uses KlinkDocumentUtils::documentTypeFromMimeType
	 *
	 * @param string $mimeType the file mime type
	 * @return string the path to the default image for that file mime type
	 */
	private function getDefaultThumbnail($mimeType){

		if(strpos($mimeType, 'audio')!==false){
			$doc_type = 'music';
		}
		else if($mimeType === 'text/uri-list'){
			$doc_type = 'web-page';
		}
		else {
			$doc_type = KlinkDocumentUtils::documentTypeFromMimeType($mimeType);
		}
        
        $path = public_path('images/' . $doc_type . '.png');
        
        if(@is_file($path)){
            return $path;
        }
        
		return public_path('images/unknown.png');

	}
}
