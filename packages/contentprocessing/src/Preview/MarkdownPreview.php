<?php

namespace KBox\Documents\Preview;

use KBox\File;
use Illuminate\Contracts\Support\Renderable;
use Markdown;

/**
 * Markdown preview.
 * Read markdown text files
 */
class MarkdownPreview extends BasePreviewDriver implements Renderable
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
        $content = $this->reader->extract('text/x-markdown', $this->path);
                 
        $content = Markdown::convertToHtml($content);

        return sprintf('<div class="preview__render preview__render--text markdown">%1$s</div>', $content);
    }

    public function supportedMimeTypes()
    {
        return [
            'text/x-markdown'
        ];
    }
}
