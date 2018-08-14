<?php

namespace KBox\Documents\ExtractText;

// use PhpOffice\PhpWord\IOFactory;
use PharData;
use KBox\Documents\Contracts\ExtractText as ExtractTextContract;

/**
 *
 */
class PresentationExtractor implements ExtractTextContract
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
        // todo: check if is a pptx and not a normal ppt

        // get the number of slides

        $slides = $this->extractSlideList();

        $slide_text = [];

        foreach ($slides as $slide) {
            $slide_text[] = $this->extractTextFromSlide($slide);
        }
        
        return implode(' ', $slide_text);
    }

    private function extractSlideList()
    {
        $archive = new PharData("$this->path/ppt/slides");
        $slides = [];

        foreach ($archive as $file) {
            if ($file->isFile()) {
                $slides[] = basename($file->getPathname());
            }
        }

        $archive = null;
        return $slides;
    }

    private function extractTextFromSlide($slide)
    {
        // extracting plain text directly from the xml file, without processing it
        $xml = file_get_contents("zip://$this->path#ppt/slides/$slide");
                
        $stripped = strip_tags($xml, '<a:r>'); //preserving paragraphs and new lines

        // collapsing into same line and returning
        return trim(preg_replace(
            "/ {2,}/",
            ' ',
            str_replace('</a:r>', ' ', str_replace('<a:r>', '', $stripped))
        ));
    }

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return ['application/vnd.openxmlformats-officedocument.presentationml.presentation'];
    }
}
