<?php

namespace Tests\Unit\Documents;

use KBox\File;
use Tests\TestCase;
use KBox\Documents\FileHelper;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Thumbnail\VideoThumbnailGenerator;

class VideoThumbnailGenerationTest extends TestCase
{
    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return factory(File::class)->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }

    public function test_mp4_file_is_supported()
    {
        $generator = new VideoThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/video.mp4');

        $this->assertTrue($generator->isSupported($file), "MP4 file not supported for thumbnail generation");
    }

    public function test_mp4_file_thumbnail_can_be_generated()
    {
        $path = 'video.mp4';
        
        Storage::fake('local');

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );

        $generator = new VideoThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
        Storage::disk('local')->assertExists('video.png');
    }
}
