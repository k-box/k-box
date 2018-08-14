<?php

namespace KBox\Documents\ExtractText;

use SplFileInfo;
use Exception;
use Symfony\Component\Debug\Exception\FatalErrorException;
use KBox\Documents\Preview\Exception\UnsupportedFileException;
use KBox\Documents\ExtractText\Exceptions\TextExtractionException;

/**
 * Load a file and select the text extractor for the specified file
 */
class ExtractTextFactory
{
    /**
     * The extension => renderer map, if the extension is not bound to
     * a text extractor, it is automatically unsupported.
     */
    const LOADER_MAPPING = [
        // office documents
        'docx' => WordExtractor::class,
        'pptx' => PresentationExtractor::class,
        
        // text documents
        'txt' => TextFileExtractor::class,
        'md' => TextFileExtractor::class,
        'markdown' => TextFileExtractor::class,
        'pdf' => PdfExtractor::class,
        'csv' => TextFileExtractor::class,
        'rtf' => RtfExtractor::class,

        // KML/KMZ format
        'kml' => KmlExtractor::class,
        // 'kmz' => KmlExtractor::class,

    ];

    /**
     * Load a file and return the correspondent text extractor
     *
     * @param string $path the path of the file
     * @param string $extesion (optional) The file extension, if cannot be deducted from the $path.
     *                         If specified will be used to find the correct preview renderer
     * @return KBox\Documents\Contract\ExtractText
     * @throws TextExtractionException if an error occurred when extracting the plain text from the file
     * @throws UnsupportedFileException if the file type is not supported
     */
    public static function load($path, $extension = null)
    {
        $info = new SplFileInfo($path);
        $extension = (! empty($extension)) ? $extension : $info->getExtension();

        if (@self::LOADER_MAPPING[$extension] !== null) {
            try {
                $class = self::LOADER_MAPPING[$extension];
                return (new $class)->load($path);
            } catch (Exception $ex) {
                throw new TextExtractionException(trans('preview::errors.unsupported_file'));
            } catch (FatalErrorException $ex) {
                \Log::error('Fatal error while generating the preview of '.$path, ['ex' => $ex]);
                throw new TextExtractionException(trans('preview::errors.preview_generation'));
            }
        }

        throw new UnsupportedFileException(trans('preview::errors.unsupported_file', [
            'file' => basename($path),
            'format' => $extension]));
    }

    /**
     * Check if a file is supported by the preview system
     *
     * @param string $path the path of the file
     * @return bool true if the file is supported by the preview service, false otherwise
     */
    public static function isFileSupported($path)
    {
        $info = new SplFileInfo($path);
        $extension = $info->getExtension();

        if (@self::LOADER_MAPPING[$extension] !== null) {
            return true;
        }

        return false;
    }
}
