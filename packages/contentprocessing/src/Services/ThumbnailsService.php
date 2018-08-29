<?php

namespace KBox\Documents\Services;

use Log;
use Imagick;
use Exception;
use KBox\File;
use KBox\Documents\DocumentType;
use KBox\Jobs\ThumbnailGenerationJob;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Thumbnail\ThumbnailImage;
use OneOffTech\VideoProcessing\VideoProcessorFactory;
use KBox\Documents\Thumbnail\ImageThumbnailGenerator;
use KBox\Documents\Exceptions\UnsupportedFileException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use KBox\Documents\Exceptions\ThumbnailGeneratorNotFoundException;

/**
 * The service responsible for the generation of the {@see File}
 * thumbnail
 *
 * This service uses the thumbnail generation endpoint offered by
 * the K-Core.
 */
class ThumbnailsService
{
    /**
     * Default thumbnail folder on disk for each file
     * @var string
     */
    const THUMBNAILS_FOLDER_NAME = 'thumbnails';

    /**
     * Default thumbnail image format
     * @var string
     */
    const THUMBNAIL_IMAGE_FORMAT = 'image/png';

    /**
     * Default thumbnail image extension
     * @var string
     */
    const THUMBNAIL_IMAGE_EXTENSION = '.png';

    /**
     * Default thumbnail image size. Define both width and height
     * @var int
     */
    const THUMBNAIL_SIZE = 300;

    /**
     * The queue to dispatch jobs on
     *
     * @var string
     */
    protected $queue = null;

    /**
     * The configured thumbnail generators
     *
     * @var array
     */
    private $generators = [
        ImageThumbnailGenerator::class,
    ];

    /**
     * Supported file mime types.
     *
     * The list is generated from the configured generators
     *
     * @var array|null
     */
    private $supportedMimeTypes = null;

    public function __construct()
    {
        $this->queue = config('contentprocessing.queue');
    }

    /**
     * Return the list of configured generators
     *
     * @return array<string>
     */
    public function generators()
    {
        return $this->generators;
    }

    /**
     * Return the list of supported mime type
     *
     * @return array<string>
     */
    public function supportedMimeTypes()
    {
        if (! is_null($this->supportedMimeTypes)) {
            return $this->supportedMimeTypes;
        }

        $mimeTypes = collect($this->generators)->map(function ($generator) {
            return (new $generator())->supportedMimeTypes();
        })->flatten()->toArray();

        return $this->supportedMimeTypes = $mimeTypes;
    }

    /**
     * Register a thumbnail generator
     *
     * @param string $generator the thumbnail class
     * @return ThumbnailsService
     */
    public function register(string $generator)
    {
        if (in_array($generator, $this->generators)) {
            return $this;
        }
        array_push($this->generators, $generator);
        
        // clean the cache of supported mime types since there
        // was a change in the generators list
        $this->supportedMimeTypes = null;
        
        return $this;
    }

    private function generatorFor(File $file)
    {
        if (! $this->isSupported($file)) {
            throw UnsupportedFileException::file($file);
        }

        // get the first generator that support the file
        $generator = collect($this->generators)->first(function ($generator) use ($file) {
            return (new $generator())->isSupported($file);
        });

        if (is_null($generator)) {
            throw ThumbnailGeneratorNotFoundException::for($file);
        }

        return new $generator();
    }

    /**
     * Generate an in-memory thumbnail representation for the given file
     *
     * @param File $file
     * @return ThumbnailImage
     */
    public function thumbnail(File $file) : ThumbnailImage
    {
        if (! $this->isSupported($file)) {
            throw UnsupportedFileException::file($file);
        }

        $generator = $this->generatorFor($file);

        return $generator->generate($file);
    }

    /**
     * Check if a given File is supported by the configured thumbnail generators.
     * The check is performed at mime type level
     * 
     * @param File $file The {@see File} to check
     * @return bool
     */
    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes());
    }

    /**
     * Immediately generate a file thumbnail and save it to the specified path
     *
     * @param File $file The {@see File} you want the thumbnail for.
     * @return File
     * @throws Exception In case after the thumbnail generation its location is not a valid file
     */
    public function generate(File $file)
    {
        $thumb_save_path = $this->getSavePath($file);
    
        try {
            $thumbnail = $this->thumbnail($file);

            $thumbnail->save($thumb_save_path);
        } catch (Exception $kex) {
            Log::error('Error generating thumbnail', ['param' => $file->toArray(), 'exception' => $kex]);

            $thumb_save_path = $this->fallback($file);
        } catch (ErrorException $kex) {
            Log::error('Error generating thumbnail', ['param' => $file->toArray(), 'exception' => $kex]);

            $thumb_save_path = $this->fallback($file);
        } catch (FatalThrowableError $kex) {
            Log::error('Error generating thumbnail', ['param' => $file->toArray(), 'exception' => $kex]);

            $thumb_save_path = $this->fallback($file);
        }

        if (! is_file($thumb_save_path)) {
            Log::error("Thumbnail file $thumb_save_path is not a valid file.", ['param' => $file->toArray()]);
            
            return $file;
        }

        $file->thumbnail_path = $thumb_save_path;

        $file->save();

        return $file;
    }

    /**
     * Queue a thumbnail generation job to be executed in an asynchrounous way
     *
     * @param File $file The {@see File} you want the thumbnail for.
     * @return ThumbnailsService
     */
    public function queue(File $file)
    {
        dispatch((new ThumbnailGenerationJob($file))->onQueue($this->queue));

        return $this;
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
     * Get the fallback thumbnail for the given file
     *
     * @param File $file
     * @return string the fallback image path with respect to the public storage disk
     */
    public function fallback(File $file)
    {
        return $this->defaultFor($file->document_type);
    }

    /**
     * Get the default thumbnail associated to a mime type
     *
     * @uses DocumentType::from
     *
     * @param string $mimeType the file mime type
     * @return string the path to the default image for that file mime type
     */
    public function defaultFor($documentType)
    {
        if ($documentType === DocumentType::WORD_DOCUMENT || $documentType === DocumentType::PDF_DOCUMENT) {
            $documentType = DocumentType::DOCUMENT;
        }
        if ($documentType === DocumentType::URI_LIST) {
            $documentType = DocumentType::WEB_PAGE;
        }

        $path = public_path("images/$documentType.png");
        
        if (@is_file($path)) {
            return $path;
        }
        
        return public_path('images/unknown.png');
    }
}
