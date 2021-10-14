<?php

namespace Tests\Unit\Documents;

use KBox\File;
use Tests\TestCase;
use KBox\Documents\DocumentType;
use Illuminate\Support\Facades\Queue;
use Klink\DmsAdapter\Traits\SwapInstance;
use KBox\Documents\Facades\Thumbnails;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Services\ThumbnailsService;
use KBox\Documents\Thumbnail\PdfThumbnailGenerator;
use KBox\Documents\Thumbnail\ImageThumbnailGenerator;
use KBox\Documents\Thumbnail\VideoThumbnailGenerator;

use KBox\Documents\Exceptions\UnsupportedFileException;
use KBox\Jobs\ThumbnailGenerationJob;
use KBox\Documents\Contracts\ThumbnailGenerator as ThumbnailGeneratorContract;

class ThumbnailServiceTest extends TestCase
{
    use  SwapInstance;

    public function mime_type_and_fallback_thumbnail_provider()
    {
        return [
            ['text/html', 'images/web-page.png'],
            ['application/msword', 'images/document.png'],
            ['application/vnd.ms-excel', 'images/spreadsheet.png'],
            ['application/vnd.ms-powerpoint', 'images/presentation.png'],
            ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'images/spreadsheet.png'],
            ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'images/presentation.png'],
            ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'images/document.png'],
            ['application/pdf', 'images/document.png'],
            ['text/uri-list', 'images/web-page.png'],
            ['image/jpg', 'images/image.png'],
            ['image/jpeg', 'images/image.png'],
            ['image/gif', 'images/image.png'],
            ['image/png', 'images/image.png'],
            ['image/tiff', 'images/image.png'],
            ['text/plain', 'images/text-document.png'],
            ['application/rtf', 'images/text-document.png'],
            ['text/x-markdown', 'images/text-document.png'],
            ['application/vnd.google-apps.document', 'images/document.png'],
            ['application/vnd.google-apps.drawing', 'images/image.png'],
            ['application/vnd.google-apps.form', 'images/form.png'],
            ['application/vnd.google-apps.fusiontable', 'images/spreadsheet.png'],
            ['application/vnd.google-apps.presentation', 'images/presentation.png'],
            ['application/vnd.google-apps.spreadsheet', 'images/spreadsheet.png'],
            ['application/vnd.google-earth.kml+xml', 'images/geodata.png'],
            ['application/vnd.google-earth.kmz', 'images/geodata.png'],
            ['application/rar', 'images/archive.png'],
            ['application/zip', 'images/archive.png'],
            ['application/x-mimearchive', 'images/web-page.png'],
            ['video/x-ms-vob', 'images/dvd-video.png'],
            ['content/DVD', 'images/dvd-video.png'],
            ['video/x-ms-wmv', 'images/video.png'],
            ['video/x-ms-wmx', 'images/video.png'],
            ['video/x-ms-wm', 'images/video.png'],
            ['video/avi', 'images/video.png'],
            ['video/divx', 'images/video.png'],
            ['video/x-flv', 'images/video.png'],
            ['video/quicktime', 'images/video.png'],
            ['video/mpeg', 'images/video.png'],
            ['video/mp4', 'images/video.png'],
            ['video/ogg', 'images/video.png'],
            ['video/webm', 'images/video.png'],
            ['video/x-matroska', 'images/video.png'],
            ['video/3gpp', 'images/video.png'],
            ['video/3gpp2', 'images/video.png'],
            ['text/csv', 'images/spreadsheet.png'],
            ['message/rfc822', 'images/email.png'],
            ['application/vnd.ms-outlook', 'images/email.png'],
            ['application/octet-stream', 'images/unknown.png'],

        ];
    }

    public function test_returns_configured_generators()
    {
        $this->swap(ThumbnailsService::class, new ThumbnailsService());

        $generators = Thumbnails::generators();

        $this->assertEquals([
            ImageThumbnailGenerator::class,
            PdfThumbnailGenerator::class,
            VideoThumbnailGenerator::class,
        ], $generators);
    }

    public function test_generator_can_be_registered()
    {
        $this->swap(ThumbnailsService::class, new ThumbnailsService());

        $configured_generators = Thumbnails::generators();

        Thumbnails::register('Class');
        
        $generators = Thumbnails::generators();

        $this->assertEquals(array_merge($configured_generators, ['Class']), $generators);
    }

    public function test_supported_mime_types_are_extracted_from_generators()
    {
        $mimeTypes = Thumbnails::supportedMimeTypes();

        $this->assertEquals([
            'image/png',
            'image/gif',
            'image/jpg',
            'image/jpeg',
            'application/pdf',
            'video/mp4',
        ], $mimeTypes);
    }

    public function test_generator_usage()
    {
        $file = factory(File::class)->create([
            'path' => __DIR__.'/../../data/example.txt',
            'mime_type' => 'ateam/mad'
        ]);

        Thumbnails::register(TestingThumbnailGenerator::class);

        $image = Thumbnails::thumbnail($file);

        $this->assertInstanceOf(ThumbnailImage::class, $image);
    }
    
    public function test_unsupported_exception_thrown_if_file_not_supported()
    {
        $file = factory(File::class)->create([
            'mime_type' => 'ateam/mad'
        ]);

        $this->expectException(UnsupportedFileException::class);

        $image = Thumbnails::thumbnail($file);
    }
    
    /**
     * @dataProvider mime_type_and_fallback_thumbnail_provider
     */
    public function test_default_thumbnail_for_mime_type($mimeType, $expected_thumb)
    {
        $documentType = DocumentType::from($mimeType);
        
        $path = Thumbnails::defaultFor($documentType);
        
        $full_expected_path = public_path($expected_thumb);
        
        $this->assertEquals($full_expected_path, $path);
        
        $this->assertTrue(@is_file($path));
        
        $this->assertTrue(@is_file($full_expected_path));
    }
    
    public function test_thumbnail_generation_job_is_dispatched()
    {
        Queue::fake();
        
        config(['contentprocessing.queue' => 'custom']);
        $this->swap(ThumbnailsService::class, new ThumbnailsService());

        $file = factory(File::class)->create([
            'mime_type' => 'ateam/mad'
        ]);
        
        Thumbnails::queue($file, '.');

        Queue::assertPushedOn('custom', ThumbnailGenerationJob::class);
    }
}

class TestingThumbnailGenerator implements ThumbnailGeneratorContract
{
    public function generate(File $file) : ThumbnailImage
    {
        if ($this->isSupported($file)) {
            return ThumbnailImage::create(100, 100, '#ccc');
        }

        return null;
    }

    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes());
    }

    public function supportedMimeTypes()
    {
        return ['ateam/mad'];
    }
}
