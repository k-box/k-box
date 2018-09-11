<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\TextPreview;

class TextPreviewTest extends TestCase
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
    
    public function testConvertToHtml()
    {
        $path = base_path('tests/data/text.txt');

        $preview = (new TextPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(TextPreview::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertContains('TXT file', $html);
        $this->assertContains('<br/>with a new line', $html);
        $this->assertContains('preview__render preview__render--text', $html);
    }
}
