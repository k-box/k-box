<?php

namespace KBox\Documents\Services;

use KBox\File;
use ReflectionClass;
use ReflectionException;
use KBox\Documents\Preview\PreviewFactory;
use Illuminate\Contracts\Support\Renderable;
use KBox\Documents\Contracts\PreviewDriver;
use KBox\Documents\Exceptions\InvalidDriverException;
use KBox\Documents\Exceptions\UnsupportedFileException;
use KBox\Documents\Preview\GoogleDrivePreview;
use KBox\Documents\Preview\MarkdownPreview;
use KBox\Documents\Preview\PresentationPreview;
use KBox\Documents\Preview\WordDocumentPreview;
use KBox\Documents\Preview\TextPreview;
use KBox\Documents\Preview\SpreadsheetPreview;
use KBox\Documents\Preview\ImagePreviewDriver;
use KBox\Documents\Preview\VideoPreviewDriver;
use KBox\Documents\Preview\PdfPreviewDriver;

/**
 * Preview service
 *
 * Generate a file preview and manages the preview drivers
 */
final class PreviewService extends ExtendableFileElaborationService
{

    /**
     * The default preview drivers
     *
     * @var array
     */
    protected $drivers = [
        TextPreview::class,
        MarkdownPreview::class,
        PdfPreviewDriver::class,
        ImagePreviewDriver::class,
        VideoPreviewDriver::class,
        WordDocumentPreview::class,
        PresentationPreview::class,
        SpreadsheetPreview::class,
        GoogleDrivePreview::class,
    ];

    /**
     * Generate a File preview
     *
     * @return Illuminate\Contracts\Support\Renderable
     * @throws PreviewGenerationException if an error occurred during the preview generation
     * @throws UnsupportedFileException if the file type is not supported
     */
    public function preview(File $file): Renderable
    {
        if (! $this->isSupported($file)) {
            throw UnsupportedFileException::file($file);
        }

        $driver = $this->driverFor($file);

        return $driver->preview($file);
    }

    /**
     * Validate the driver class
     */
    protected function validateDriver($driverClass)
    {
        try {
            (new ReflectionClass($driverClass))->isSubclassOf(PreviewDriver::class);
        } catch (ReflectionException $ex) {
            throw InvalidDriverException::classNotImplements($driverClass, PreviewDriver::class);
        }
    }

    // /**
    //  * Load a file and return the correspondent preview renderer
    //  *
    //  * @param string $path the path of the file
    //  * @param string $extesion (optional) The file extension, if cannot be deducted from the $path.
    //  *                         If specified will be used to find the correct preview renderer
    //  * @return KBox\Documents\Contract\Preview
    //  */
    // public function load($path, $extension = null)
    // {
    //     return PreviewFactory::load($path, $extension);
    // }

    // /**
    //  * Check if a file is supported by the preview system
    //  *
    //  * @param string $path the path of the file
    //  * @return bool true if the file is supported by the preview service, false otherwise
    //  */
    // public function isFileSupported($path)
    // {
    //     return PreviewFactory::isFileSupported($path);
    // }
}
