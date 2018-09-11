<?php

namespace KBox\Documents\Preview;

use KBox\File;
use Illuminate\Contracts\Support\Renderable;

/**
 * Text preview.
 * Reads plain text files
 */
class TextPreview extends BasePreviewDriver implements Renderable
{
    private $path = null;

    private $reader = null;
    
    public function __construct()
    {
    }

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
        $content = $this->reader->extract('text/plain', $this->path);
                 
        $content = str_replace("\n", '<br/>', $content);

        return sprintf('<div class="preview__render preview__render--text">%1$s</div>', $content);
    }

    public function supportedMimeTypes()
    {
        return [
            'text/plain'
        ];
    }
}
