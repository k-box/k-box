<?php

namespace Tests\Unit\Documents;

use Tests\TestCase;
use KBox\Documents\Thumbnail\ThumbnailImage;

class ThumbnailImageTest extends TestCase
{
    public function test_empty_canvas_can_be_created_and_draw()
    {
        $image = ThumbnailImage::create(100, 100, '#ccc');

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(100, $image->width());
    }
    
    public function test_instance_from_file()
    {
        $image = ThumbnailImage::load(__DIR__.'/../../data/project-avatar.png');
        
        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(100, $image->width());
        $this->assertEquals(50, $image->resize(50, null, function ($constraint) {
            $constraint->aspectRatio();
        })->width());
    }
}
