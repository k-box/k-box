<?php namespace Content\Preview;


use SplFileInfo;
use Exception;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Content\Preview\Exception\UnsupportedFileException;
use Content\Preview\Exception\PreviewGenerationException;

/**
 * Load a file and select the preview renderer
 */
class PreviewFactory
{
    /**
     * The extension => renderer map, if the extension is not bound to 
     * a preview rendered, it is automatically unsupported.
     */
    const LOADER_MAPPING = [
        // office documents
        'docx' => WordDocumentPreview::class,
        'xlsx' => SpreadsheetPreview::class,
        'csv' => SpreadsheetPreview::class,
        'pptx' => PresentationPreview::class,

        'rtf' => WordDocumentPreview::class,
        
        // text documents
        'txt' => TextPreview::class,
        'md' => MarkdownPreview::class,
        'markdown' => MarkdownPreview::class,

        // google drive format
        'gdoc' => GoogleDrivePreview::class,
        'gslides' => GoogleDrivePreview::class,
        'gsheet' => GoogleDrivePreview::class,

    ];

    /**
     * Load a file and return the correspondent preview renderer
     *
     * @param string $path the path of the file
     * @param string $extesion (optional) The file extension, if cannot be deducted from the $path. 
     *                         If specified will be used to find the correct preview renderer
     * @return Content\Contract\Preview
     * @throws PreviewGenerationException if an error occurred during the preview generation
     * @throws UnsupportedFileException if the file type is not supported
     */
    public static function load($path, $extension = null)
    {
        $info = new SplFileInfo($path);
        $extension = (!empty($extension)) ? $extension : $info->getExtension();

        if(@self::LOADER_MAPPING[$extension] !== null)
        {
            try
            {
                $class = self::LOADER_MAPPING[$extension];
                return (new $class)->load($path);

            }
            catch (Exception $ex)
            {
                throw new PreviewGenerationException(trans('preview::errors.unsupported_file'));
            }
            catch (FatalErrorException $ex)
            {
                \Log::error('Fatal error while generating the preview of ' . $path, ['ex' => $ex]);
                throw new PreviewGenerationException(trans('preview::errors.preview_generation'));
            }
        }

        throw new UnsupportedFileException( trans('preview::errors.unsupported_file', [
            'file' => basename($path), 
            'format' => $extension]) );
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

        if(@self::LOADER_MAPPING[$extension] !== null)
        {
            return true;
        }

        return false;
    }
    
}
