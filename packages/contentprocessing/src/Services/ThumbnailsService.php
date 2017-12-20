<?php

namespace Content\Services;

use KBox\File;

use Klink\DmsAdapter\Contracts\KlinkAdapter;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Log;
use Exception;
use Imagick;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use OneOffTech\VideoProcessing\VideoProcessorFactory;
use Klink\DmsAdapter\KlinkImageResize;

/**
 * The service responsible for the generation of the {@see File}
 * thumbnail
 *
 * This service uses the thumbnail generation endpoint offered by
 * the K-Core.
 */
class ThumbnailsService
{
    const THUMBNAILS_FOLDER_NAME = 'thumbnails';
    const THUMBNAIL_IMAGE_FORMAT = 'image/png';
    const THUMBNAIL_IMAGE_EXTENSION = '.png';
    const THUMBNAIL_SIZE = 300;

    /**
     * Supported file mime types.
     *
     * If the file mime type is not here, a tentative, default thumbnail
     * is returned
     */
    private static $supportedMime = [
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
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'video/mp4'
        ];

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
        if (! is_null($file->thumbnail_path) && ! $force) {
            return $file->absolute_thumbnail_path;
        }
        
        // get file mime type
        $charset_pos = strpos($file->mime_type, ';');
        $mime = $charset_pos !== false ? trim(substr($file->mime_type, 0, $charset_pos)) : $file->mime_type;

        Log::info("Processing thumbnail generation for file {$file->id} ({$mime})...");

        $default_path = $this->getDefaultThumbnail($mime);

        // check if the mimetype is included in the supported list

        if (! in_array($mime, self::$supportedMime)) {
            Log::warning("File {$file->id} with mime type {$mime} not supported. Returning default thumbnail.");

            $file->thumbnail_path = $default_path;

            $file->save();

            return $default_path;
        }

        $thumb_save_path = $this->getSavePath($file);
    
        try {
            if ($mime === 'image/jpg' || $mime === 'image/jpeg' || $mime === 'image/png') {
                $thumb_save_path = $this->generateImageThumbnail($mime, $file->absolute_path, $thumb_save_path);
            } elseif ($mime === 'application/pdf') {
                $thumb_save_path = $this->generatePdfThumbnail($mime, $file->absolute_path, $thumb_save_path);
            } elseif ($mime === 'video/mp4') {
                $videoProcessor = app()->make(VideoProcessorFactory::class)->make();
                
                $out = $videoProcessor->thumbnail($file->absolute_path);

                $thumb_save_path = dirname($file->absolute_path).'/'.str_replace('.mp4', '.png', basename($file->absolute_path));
            } else {
                $thumb_save_path = $this->getDefaultThumbnail($mime);
            }
        } catch (Exception $kex) {
            Log::error('Error generating thumbnail', ['param' => $file->toArray(), 'exception' => $kex]);

            $thumb_save_path = $this->getDefaultThumbnail($mime);
        } catch (ErrorException $kex) {
            Log::error('Error generating thumbnail', ['param' => $file->toArray(), 'exception' => $kex]);

            $thumb_save_path = $this->getDefaultThumbnail($mime);
        } catch (FatalThrowableError $kex) {
            Log::error('Error generating thumbnail', ['param' => $file->toArray(), 'exception' => $kex]);

            $thumb_save_path = $this->getDefaultThumbnail($mime);
        }

        if (! is_file($thumb_save_path)) {
            Log::error("Thumbnail file $thumb_save_path is not a valid file.", ['param' => $file->toArray()]);
            throw new Exception('Thumbnail not saved');
        }

        // saving back everything

        $file->thumbnail_path = $thumb_save_path;

        $file->save();

        return $thumb_save_path;
    }

    private function generateImageThumbnail($mime, $filePath, $savePath)
    {
        $image = new KlinkImageResize();
        
        $image->load($filePath);
        $image->resizeToWidth(self::THUMBNAIL_SIZE);
        $fileContent = $image->get(IMAGETYPE_PNG);
        
        file_put_contents($savePath, $fileContent);

        return $savePath;
    }

    private function generatePdfThumbnail($mime, $filePath, $savePath)
    {
        // check if imagemagick is installed
        if (! extension_loaded('imagick') && ! class_exists('Imagick')) {
            throw new Exception('Failed to generate pdf thumbnail: imagemagick is not installed');
        }

        $image = new Imagick();
        $image->setResolution(300, 300); // forcing resolution to 300dpi prevents mushy images
        $image->readImage($filePath.'[0]'); // file.pdf[0] refers to the first page of the pdf
        $image->setImageBackgroundColor('#ffffff'); // do not create transparent thumbnails
        $image->resizeImage(self::THUMBNAIL_SIZE, self::THUMBNAIL_SIZE, Imagick::FILTER_LANCZOS, 1); // best interpolation
        $image->setImageFormat("png"); // save as png, TODO: respect THUMBNAIL_IMAGE_EXTENSION
        $image->writeImage($savePath);
        $image->clear(); // free memory

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
        $dir = dirname($file->absolute_path).'/'.self::THUMBNAILS_FOLDER_NAME.'/';

        $is_dir = is_dir($dir);

        if (! $is_dir) {
            // create containing folder
            $is_dir = mkdir($dir, 0755, true);

            if (! $is_dir) {
                Log::error("Cannot create thumbnail folder $dir");

                $dir = dirname($file->absolute_path).'/';
            }
        }

        return $dir.substr($file->hash, 0, 42).self::THUMBNAIL_IMAGE_EXTENSION;
    }

    /**
     * Get the default thumbnail associated to a mime type
     *
     * @uses KlinkDocumentUtils::documentTypeFromMimeType
     *
     * @param string $mimeType the file mime type
     * @return string the path to the default image for that file mime type
     */
    private function getDefaultThumbnail($mimeType)
    {
        if (strpos($mimeType, 'audio')!==false) {
            $doc_type = 'music';
        } elseif ($mimeType === 'text/uri-list') {
            $doc_type = 'web-page';
        } else {
            $doc_type = KlinkDocumentUtils::documentTypeFromMimeType($mimeType);
        }
        
        $path = public_path('images/'.$doc_type.'.png');
        
        if (@is_file($path)) {
            return $path;
        }
        
        return public_path('images/unknown.png');
    }
}
