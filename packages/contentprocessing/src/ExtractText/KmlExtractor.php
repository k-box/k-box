<?php

namespace KBox\Documents\ExtractText;

use KBox\Documents\Contracts\ExtractText as ExtractTextContract;

/**
 *
 */
class KmlExtractor implements ExtractTextContract
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
        
        $utf8_content = mb_convert_encoding(
            $content,
            'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)
        );
        
        $name_preg_int = preg_match_all('/<name>(.*)<\/name>/', $utf8_content, $name_matches);
        
        $descr_preg_int = preg_match_all('/<description>(.*)<\/description>/', $utf8_content, $description_matches);
        
        $names = '';
        $descriptions = '';
        
        if ($name_preg_int > 0) {
            $names = implode(' ', $name_matches[1]);
        }
        
        if ($descr_preg_int > 0) {
            $descriptions = implode(' ', $description_matches[1]);
        }
        
        return $names.' '.$descriptions;
    }

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return ['application/vnd.google-earth.kml+xml'];
    }
}
