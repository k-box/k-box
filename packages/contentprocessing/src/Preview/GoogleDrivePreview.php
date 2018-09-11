<?php

namespace KBox\Documents\Preview;

use KBox\File;
use KBox\Documents\Preview\Exception\PreviewGenerationException;
use Illuminate\Contracts\Support\Renderable;

/**
 * GoogleDrive preview.
 * Read Google Drive pointer files: .gdoc, .gslides, .gsheet
 */
class GoogleDrivePreview extends BasePreviewDriver implements Renderable
{
    private $path = null;

    private $reader = null;
    
    protected function load($path)
    {
        $this->path = $path;

        $this->reader = app()->make('KBox\Documents\FileContentExtractor');

        return $this;
    }

    public function preview(File $file) : Renderable
    {
        $this->load($file->absolute_path);

        return $this;
    }

    public function render()
    {
        $content = $this->reader->openAsText($this->path);

        $decoded = json_decode($content);
        
        if ($decoded !== false) {
            return sprintf(
                '<div class="preview__render preview__render--googledrive"><svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 48 48"><path fill="#b6b6b6" d="M38.71 20.07C37.35 13.19 31.28 8 24 8c-5.78 0-10.79 3.28-13.3 8.07C4.69 16.72 0 21.81 0 28c0 6.63 5.37 12 12 12h26c5.52 0 10-4.48 10-10 0-5.28-4.11-9.56-9.29-9.93z"/></svg><h5>%3$s</h5><a class="button button-primary" rel="noopener nofollow" href="%1$s" target="_blank">%2$s</a></div>',
                    $decoded->url,
                    trans('documents.preview.open_in_google_drive_btn'),
                    trans('documents.preview.google_file_disclaimer_alt')
            );
        }

        throw new PreviewGenerationException('Unable to gather Google Drive link from the file');
    }

    public function supportedMimeTypes()
    {
        return [
            'application/vnd.google-apps.document',
            'application/vnd.google-apps.drawing',
            'application/vnd.google-apps.form',
            'application/vnd.google-apps.fusiontable',
            'application/vnd.google-apps.presentation',
            'application/vnd.google-apps.spreadsheet',
        ];
    }
}
