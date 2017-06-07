<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Content\Preview\WordDocumentPreview;
use Content\FileProperties;

class WordDocumentPreviewTest extends TestCase
{
    
    public function testConvertDocxToHtml()
    {
        $path = __DIR__ . '/data/example.docx';

        $preview = (new WordDocumentPreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(WordDocumentPreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNotNull($preview->properties());
        $this->assertInstanceOf(FileProperties::class, $preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('<p>Example document for unit tests</p>', $html);
        $this->assertContains('preview__render preview__render--document', $html);
    }
    
}
