<?php

namespace KBox\Documents\ExtractText;

use KBox\Documents\Contracts\ExtractText as ExtractTextContract;

/**
 *
 */
class TextFileExtractor implements ExtractTextContract
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
        $content = file_get_contents($this->path);
                
        return $utf8_content = mb_convert_encoding(
            $content,
            'UTF-8',
                        mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)
        );
    }

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return ['text/x-markdown', 'plain/text', 'text/csv'];
    }
}
