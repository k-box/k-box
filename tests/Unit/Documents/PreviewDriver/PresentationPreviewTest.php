<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\PresentationPreview;
use KBox\Documents\FileProperties;
use KBox\Documents\Presentation\PresentationProperties;

class PresentationPreviewTest extends TestCase
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
    
    public function testConvertPptxToHtml()
    {
        $path = base_path('tests/data/presentation.pptx');

        $preview = (new PresentationPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(PresentationPreview::class, $preview);
        // $this->assertNotNull($preview->properties());
        // $this->assertInstanceOf(FileProperties::class, $preview->properties());
        // $this->assertInstanceOf(PresentationProperties::class, $preview->properties());
        // $this->assertEquals(4, $preview->properties()->totalSlides());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('slides', $html);
    }
}
