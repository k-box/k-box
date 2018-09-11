<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\WordDocumentPreview;

class WordDocumentPreviewTest extends TestCase
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
    
    public function testConvertDocxToHtml()
    {
        $path = __DIR__.'/data/example.docx';

        $preview = (new WordDocumentPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(WordDocumentPreview::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('<p>Example document for unit tests</p>', $html);
        $this->assertContains('preview__render preview__render--document', $html);
    }
}
