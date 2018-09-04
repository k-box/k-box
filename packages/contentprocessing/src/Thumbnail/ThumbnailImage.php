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
     * @return \KBox\Documents\ThumbnailImage
     */
    public static function create($width, $height, $background = null)
    {
        return new self(ImageFacade::canvas($width, $height, $background = null));
    }

    /**
     * Instantiate a thumbnail image from:
     *
     * - string - Path of the image in filesystem.
     * - string - URL of an image (allow_url_fopen must be enabled).
     * - string - Binary image data.
     * - string - Data-URL encoded image data.
     * - string - Base64 encoded image data.
     * - resource - PHP resource of type gd. (when using GD driver)
     * - object - Imagick instance (when using Imagick driver)
     * - object - Intervention\Image\Image instance
     * - object - SplFileInfo instance
     *
     * @see http://image.intervention.io/api/make
     * @param mixed $source
     * @return \KBox\Documents\ThumbnailImage
     */
    public static function load($source)
    {
        return new self(ImageFacade::make($source));
    }
}
