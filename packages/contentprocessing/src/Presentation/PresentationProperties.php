<?php

namespace Content\Presentation;

use Content\FileProperties;

/**
 * Presentation file properties
 *
 * @see FileProperties
 */
class PresentationProperties extends FileProperties
{
    private $totalSlides;
    private $layout;
    private $height;
    private $width;

    public function totalSlides()
    {
        return $this->totalSlides;
    }

    public function layout()
    {
        return $this->layout;
    }

    /**
    * The presentation height in millimeters
    */
    public function height()
    {
        return $this->height;
    }

    /**
    * The presentation width in millimeters
    */
    public function width()
    {
        return $this->width;
    }

    public function setTotalSlides($value)
    {
        $this->totalSlides = $value;
        return $this;
    }

    public function setLayout($value)
    {
        $this->layout = $value;
        return $this;
    }

    /**
    * The presentation height in millimeters
    */
    public function setHeight($value)
    {
        $this->height = $value;
        return $this;
    }

    /**
    * The presentation width in millimeters
    */
    public function setWidth($value)
    {
        $this->width = $value;
        return $this;
    }
}
