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
    protected $drivers = [];

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

        $generator = $this->driverFor($file);

        return $generator->render($file);
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
