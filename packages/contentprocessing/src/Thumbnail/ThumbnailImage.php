<?php

namespace KBox\Documents\Thumbnail;

use Intervention\Image\Image;
use Intervention\Image\Facades\Image as ImageFacade;

/**
 * An image that represents a thumbnail.
 *
 * This is an in-memory image representation of the thumbnail
 * that wraps the Intervention/Image/Image class.
 *
 * It is used to define the interface of a thumbnail generator in
 * an agnostic way from the filesystem and save method
 */
class ThumbnailImage
{
    const DEFAULT_WIDTH = 300;

    /**
     * @var \Intervention\Image\Image
     */
    private $image;
    
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Dynamically call the {@see /Intervention/Image/Image} instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $result = $this->image->$method(...$parameters);

        if (is_a($result, Image::class)) {
            return $this;
        }
        
        return $result;
    }

    /**
     * Creates an empty image canvas to draw on
     *
     * @param  int   $width
     * @param  int   $height
     * @param  mixed $background
     * @return \KBox\Documents\Thumbnail
     */
    public static function create($width, $height, $background = null)
    {
        return new self(ImageFacade::canvas($width, $height, $background = null));
    }

    /**
     * Instantiate a thumbnail image from a file on the filesystem
     */
    public static function load($path)
    {
        return new self(ImageFacade::make($path));
    }
}
