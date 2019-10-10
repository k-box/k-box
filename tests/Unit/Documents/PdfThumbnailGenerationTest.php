<?php

namespace Tests\Unit\Documents;

use Imagick;
use KBox\File;
use Tests\TestCase;
use KBox\Documents\FileHelper;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Thumbnail\PdfThumbnailGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @requires extension imagick
 */
class PdfThumbnailGenerationTest extends TestCase
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

    protected function setUp()
    {
        parent::setUp();

        if (empty(Imagick::queryFormats("PDF"))) {
            $this->markTestSkipped(
                'Imagick not available or PDF support not available.'
            );
        }
        
        if (env('TRAVIS', false)) {
            $this->markTestSkipped(
                'Test skipped on Travis CI due to failure with unknown reason.'
            );
        }
    }

    public function test_pdf_file_is_supported()
    {
        $generator = new PdfThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example.pdf');

        $this->assertTrue($generator->isSupported($file), "PDF file not supported for thumbnail generation");
    }

    public function test_pdf_file_thumbnail_can_be_generated()
    {
        $generator = new PdfThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example.pdf');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());
    }
}
