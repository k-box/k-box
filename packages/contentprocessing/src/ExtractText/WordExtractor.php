<?php

namespace KBox\Documents\ExtractText;

// use PhpOffice\PhpWord\IOFactory;
use KBox\Documents\Contracts\ExtractText as ExtractTextContract;

/**
 *
 */
class WordExtractor implements ExtractTextContract
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
        // todo: check if is a docx and not a normal doc

        // extracting plain text directly from the xml file, without processing it
        $xml = file_get_contents("zip://$this->path#word/document.xml");

        $stripped = strip_tags($xml, '<w:rPr>'); //preserving paragraphs and new lines
        
        // collapsing into same line and returning
        return trim(preg_replace("/ {2,}/", ' ', str_replace('<w:rPr></w:rPr>', ' ', $stripped)));
    }

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    }
}
