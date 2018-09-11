<?php

namespace Tests\Unit\Documents;

use KBox\File;
use Tests\TestCase;
use KBox\Documents\Facades\Previews;
use KBox\Documents\Services\PreviewService;
use KBox\Documents\Contracts\PreviewDriver;
use Illuminate\Contracts\Support\Renderable;
use KBox\Documents\Exceptions\UnsupportedFileException;
use KBox\Documents\Exceptions\InvalidDriverException;
use KBox\Documents\Preview\GoogleDrivePreview;
use KBox\Documents\Preview\MarkdownPreview;
use KBox\Documents\Preview\PresentationPreview;
use KBox\Documents\Preview\WordDocumentPreview;
use KBox\Documents\Preview\ImagePreviewDriver;
use KBox\Documents\Preview\VideoPreviewDriver;
use KBox\Documents\Preview\PdfPreviewDriver;
use KBox\Documents\Preview\TextPreview;
use KBox\Documents\Preview\SpreadsheetPreview;

class PreviewServiceTest extends TestCase
{
    public function test_invalid_driver_cannot_be_registered()
    {
        $this->expectException(InvalidDriverException::class);

        Previews::register('Class');
    }
    
    public function test_returns_configured_default_drivers()
    {
        $this->swap(PreviewService::class, new PreviewService());

        $drivers = Previews::drivers();

        $this->assertEquals([
            TextPreview::class,
            MarkdownPreview::class,
            PdfPreviewDriver::class,
            ImagePreviewDriver::class,
            VideoPreviewDriver::class,
            WordDocumentPreview::class,
            PresentationPreview::class,
            SpreadsheetPreview::class,
            GoogleDrivePreview::class,
        ], $drivers);
    }

    public function test_driver_can_be_registered()
    {
        $this->swap(PreviewService::class, new PreviewService());

        $configured_drivers = Previews::drivers();

        Previews::register(TestPreviewDriver::class);
        
        $drivers = Previews::drivers();

        $this->assertEquals(array_merge($configured_drivers, [TestPreviewDriver::class]), $drivers);
    }

    public function test_supported_mime_types_are_extracted_from_drivers()
    {
        $mimeTypes = Previews::supportedMimeTypes();

        $this->assertEquals([
            'text/plain',
            'text/x-markdown',
            'application/pdf',
            'image/jpeg',
            'image/gif',
            'image/png',
            'video/mp4',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'text/csv',
            'text/tab-separated-values',
            'application/vnd.google-apps.document',
            'application/vnd.google-apps.drawing',
            'application/vnd.google-apps.form',
            'application/vnd.google-apps.fusiontable',
            'application/vnd.google-apps.presentation',
            'application/vnd.google-apps.spreadsheet',
        ], $mimeTypes);
    }

    public function test_generator_usage()
    {
        $file = factory(File::class)->create([
            'path' => __DIR__.'/../../data/example.txt',
            'mime_type' => 'ateam/mad'
        ]);

        Previews::register(TestPreviewDriver::class);

        $preview = Previews::preview($file);

        $this->assertInstanceOf(Renderable::class, $preview);
    }
    
    public function test_unsupported_exception_thrown_if_file_not_supported()
    {
        $file = factory(File::class)->create([
            'mime_type' => 'ateam/mad'
        ]);

        $this->expectException(UnsupportedFileException::class);

        $preview = Previews::preview($file);
    }
}

class TestPreviewable implements Renderable
{
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function render()
    {
        return file_get_contents($file->absolute_path);
    }
}

class TestPreviewDriver implements PreviewDriver
{
    public function preview(File $file) : Renderable
    {
        return new TestPreviewable($file);
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
