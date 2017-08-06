<?php

namespace Content\Preview;

use Content\Contracts\Preview as PreviewContract;
use Markdown;

/**
 * Markdown preview.
 * Read markdown text files
 */
class MarkdownPreview implements PreviewContract
{
    private $path = null;

    private $reader = null;
    
    public function __construct()
    {
    }

    public function load($path)
    {
        $this->path = $path;

        $this->reader = app()->make('Klink\DmsDocuments\FileContentExtractor');

        return $this;
    }

    public function css()
    {
        return null;
    }

    public function html()
    {
        $content = $this->reader->extract('text/x-markdown', $this->path);
                 
        $content = Markdown::convertToHtml($content);

        return sprintf('<div class="preview__render preview__render--text">%1$s</div>', $content);
    }

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return [];
    }
}
