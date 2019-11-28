<?php

namespace Tests\Unit\Documents;

use Imagick;
use KBox\File;
use Tests\TestCase;
use KBox\Documents\FileHelper;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Thumbnail\PdfThumbnailGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;

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
    }

    /**
     * Compare the generated thumbnail with a reference file.
     *
     * It uses the absolute error with a fuzz amount of 2%.
     *
     * In other words it count pixels that differ by more than fuzz amount.
     *
     * @param string $reference the path to the reference file
     * @param string $thumbnail the path to the file to compare
     */
    private function compareThumbnailToReference($reference, $thumbnail, $differenceSavePath = null)
    {
        // init the image objects
        $image1 = new Imagick();
        $image2 = new Imagick();

        // set the fuzz factor (must be done BEFORE reading in the images)
        $image1->setOption('fuzz', '2%');

        // read in the images
        $image1->readImage($reference);
        $image2->readImage($thumbnail);

        // compare the images using METRIC=1 (Absolute Error)
        $result = $image1->compareImages($image2, 1);

        $is_different = $result[1] > 0;

        if ($is_different && $differenceSavePath) {
            ThumbnailImage::load($result[0])->save($differenceSavePath);
        }

        $this->assertFalse($is_different, "Images are not equals. Absolute error is {$result[1]}");
    }

    public function test_pdf_file_is_supported()
    {
        $generator = new PdfThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example.pdf');

        $this->assertTrue($generator->isSupported($file), "PDF file not supported for thumbnail generation");
    }

    public function test_pdf_file_thumbnail_can_be_generated()
    {
        Storage::fake('local');

        $generator = new PdfThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example.pdf');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());

        $path = Storage::disk('local')->path('example-thumbnail.png');
        $image->save($path);

        $this->compareThumbnailToReference(__DIR__.'/../../data/example-thumbnail.png', $path, Storage::disk('local')->path('example-thumbnail-difference.png'));
    }
    
    public function test_generation_of_thumbnail_for_multipage_pdf()
    {
        Storage::fake('local');

        $generator = new PdfThumbnailGenerator();

        $file = $this->createFileForPath(__DIR__.'/../../data/example-with-images-and-multiple-pages.pdf');

        $image = $generator->generate($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
        $this->assertEquals(300, $image->width());

        $path = Storage::disk('local')->path('example-with-images-and-multiple-pages-thumbnail.png');
        $image->save($path);

        $this->compareThumbnailToReference(__DIR__.'/../../data/example-with-images-and-multiple-pages-thumbnail.png', $path);
    }
}
