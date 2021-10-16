<?php

namespace Tests\Plugins\Geo\Unit;

use Imagick;
use KBox\File;
use Tests\TestCase;
use KBox\Geo\Gdal\Gdal;
use KBox\Documents\FileHelper;
use KBox\Documents\Thumbnail\ThumbnailImage;

use KBox\Geo\Thumbnails\GeoJsonGpxKmlThumbnailGenerator;

/**
 * @requires extension imagick
 */
class GeoJsonGpxKmlThumbnailGeneratorTest extends TestCase
{
    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return File::factory()->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (env('TRAVIS', false)) {
            $this->markTestSkipped(
                'Test skipped on Travis CI due to failure with unknown reason.'
            );
        }

        if (! (new Gdal())->isInstalled()) {
            $this->markTestSkipped(
                'GDal library not installed the system.'
            );
        }
    }

    public function test_gpx_is_supported()
    {
        $path = base_path('tests/data/gpx.gpx');

        $generator = new GeoJsonGpxKmlThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $isSupported = $generator->isSupported($file);

        $this->assertTrue($isSupported, "gpx not supported");
    }

    public function test_kml_is_supported()
    {
        $path = base_path('tests/data/kml.kml');

        $generator = new GeoJsonGpxKmlThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $isSupported = $generator->isSupported($file);

        $this->assertTrue($isSupported, "kml not supported");
    }

    public function test_geojson_is_supported()
    {
        $path = base_path('tests/data/geojson.geojson');

        $generator = new GeoJsonGpxKmlThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $isSupported = $generator->isSupported($file);

        $this->assertTrue($isSupported, "geojson not supported");
    }

    public function test_geojson_thumbnail_is_generated()
    {
        $path = base_path('tests/data/geojson.geojson');

        $generator = new GeoJsonGpxKmlThumbnailGenerator();

        $file = $this->createFileForPath($path);

        \Log::info("Is $path readable?", ['readable' => is_readable($path), 'file' => $file]);

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }

    public function test_kml_thumbnail_is_generated()
    {
        $path = base_path('tests/data/kml.kml');

        $generator = new GeoJsonGpxKmlThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }

    public function test_kmz_thumbnail_is_generated()
    {
        $path = base_path('tests/data/kmz.kmz');

        $generator = new GeoJsonGpxKmlThumbnailGenerator();

        $file = $this->createFileForPath($path);

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }
}
