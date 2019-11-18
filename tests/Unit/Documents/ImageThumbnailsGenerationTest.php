<?php

namespace Tests\Unit\Documents;

use KBox\File;
use Tests\TestCase;
use KBox\Documents\FileHelper;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Thumbnail\ImageThumbnailGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImageThumbnailsGenerationTest extends TestCase
{
    use DatabaseTransactions;

    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return factory(File::class)->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }

    public function test_png_file_is_supported()
    {
        $generator = new ImageThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/project-avatar.png');

        $this->assertTrue($generator->isSupported($file), "PNG file not supported for thumbnail generation");
    }
  
    public function test_png_file_thumbnail_can_be_generated()
    {
        $generator = new ImageThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/project-avatar.png');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
        $this->assertEquals('image/png', $image->mime());
    }
  
    public function test_jpg_file_thumbnail_can_be_generated()
    {
        $generator = new ImageThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example.jpg');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }
  
    public function test_vertical_jpg_file_thumbnail_can_be_generated()
    {
        $generator = new ImageThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example-vertical.jpg');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        
        $this->assertEquals(300, $image->width());
        $this->assertEquals((320/100) * 300, $image->height());
    }
  
    public function test_gif_file_thumbnail_can_be_generated()
    {
        $generator = new ImageThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example.gif');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }
}
