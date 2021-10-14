<?php

namespace Tests\Plugins\Geo\Unit;

use Imagick;
use KBox\File;
use Tests\TestCase;
use KBox\Geo\Gdal\Gdal;
use KBox\Documents\FileHelper;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Geo\Thumbnails\GeoTiffThumbnailGenerator;

class GeoTiffThumbnailGeneratorTest extends TestCase
{
    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return factory(File::class)->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('imagick') || (extension_loaded('imagick') && empty(Imagick::queryFormats("TIFF")))) {
            $this->markTestSkipped(
                'Imagick not available or TIFF support not available.'
            );
        }

        if (! (new Gdal())->isInstalled()) {
            $this->markTestSkipped(
                'GDal library not installed the system.'
            );
        }
    }

    public function test_tiff_is_supported()
    {
        $path = base_path('tests/data/geotiff.tiff');

        $generator = new GeoTiffThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $isSupported = $generator->isSupported($file);

        $this->assertTrue($isSupported, "tiff not supported");
    }

    public function test_tiff_thumbnail_is_generated()
    {
        $path = base_path('tests/data/geotiff.tiff');

        $generator = new GeoTiffThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }
}
