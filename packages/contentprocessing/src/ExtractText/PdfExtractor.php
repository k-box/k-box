<?php

namespace Content\ExtractText;

use Content\Pdf\PdfCli;
use Content\Contracts\ExtractText as ExtractTextContract;

/**
 *
 */
class PdfExtractor implements ExtractTextContract
{
    private $path = null;

    private $reader = null;
    
    public function __construct()
    {
    }

    public function load($path)
    {
        $this->path = $path;

        return $this;
    }

    public function text()
    {
        $cli = new PdfCli();
        
        $content = $cli->convertToText($this->path);
                
        return $content;
    }

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return ['text/x-markdown', 'plain/text'];
    }
}
