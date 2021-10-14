<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;

use KBox\Documents\Preview\PresentationPreview;

class PresentationPreviewTest extends TestCase
{
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
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('slides', $html);
    }
}
