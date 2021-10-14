<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;

use KBox\Documents\Preview\TextPreview;

class TextPreviewTest extends TestCase
{
    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return factory(File::class)->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }
    
    public function testConvertToHtml()
    {
        $path = base_path('tests/data/text.txt');

        $preview = (new TextPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(TextPreview::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('TXT file', $html);
        $this->assertStringContainsString('<br/>with a new line', $html);
        $this->assertStringContainsString('preview__render preview__render--text', $html);
    }
}
