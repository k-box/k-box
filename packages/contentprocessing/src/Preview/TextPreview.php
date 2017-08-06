<?php

namespace Content\Preview;

use Content\Contracts\Preview as PreviewContract;

/**
 * Text preview.
 * Reads plain text files
 */
class TextPreview implements PreviewContract
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
        $content = $this->reader->extract('text/plain', $this->path);
                 
        $content = str_replace("\n", '<br/>', $content);

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
