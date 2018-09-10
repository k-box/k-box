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

class PreviewServiceTest extends TestCase
{
    // public function file_class_provider()
    // {
    //     return [
    //         [__DIR__.'/data/presentation.pptx', 'KBox\Documents\Preview\PresentationPreview'],
    //         [__DIR__.'/data/spreadsheet.xlsx', 'KBox\Documents\Preview\SpreadsheetPreview'],
    //         [__DIR__.'/data/example.docx', 'KBox\Documents\Preview\WordDocumentPreview'],
    //         [__DIR__.'/data/csv1.csv', 'KBox\Documents\Preview\SpreadsheetPreview'],
    //         [__DIR__.'/data/text.txt', 'KBox\Documents\Preview\TextPreview'],
    //         [__DIR__.'/data/markdown.md', 'KBox\Documents\Preview\MarkdownPreview'],
    //         [__DIR__.'/data/googe-drive-doc.gdoc', 'KBox\Documents\Preview\GoogleDrivePreview'],
    //         [__DIR__.'/data/googe-drive-presentation.gslides', 'KBox\Documents\Preview\GoogleDrivePreview'],
    //         [__DIR__.'/data/googe-drive-spreadsheet.gsheet', 'KBox\Documents\Preview\GoogleDrivePreview'],
    //     ];
    // }

    // public function unsupported_file_class_provider()
    // {
    //     return [
    //         [__DIR__.'/data/compressed.zip'],
    //         [__DIR__.'/data/a-pdf.pdf'],
    //         [__DIR__.'/data/keyhole-markup.kml'],
    //         [__DIR__.'/data/keyhole-markup.kmz'],
    //     ];
    // }
    
    public function test_invalid_driver_cannot_be_registered()
    {
        $this->expectException(InvalidDriverException::class);

        Previews::register('Class');
    }
    
    public function test_returns_configured_default_drivers()
    {
        $this->swap(PreviewService::class, new PreviewService());

        $drivers = Previews::drivers();

        $this->assertEquals([], $drivers);
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

        $this->assertEquals([], $mimeTypes);
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
