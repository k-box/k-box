<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;

use KBox\Documents\Preview\WordDocumentPreview;

class WordDocumentPreviewTest extends TestCase
{
    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return File::factory()->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }
    
    public function testConvertDocxToHtml()
    {
        $path = base_path('tests/data/example.docx');

        $preview = (new WordDocumentPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(WordDocumentPreview::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('<p style="margin-top: 0; margin-bottom: 0;">Example document for unit tests</p>', $html);
        $this->assertStringContainsString('preview__render preview__render--document', $html);
    }
}
