<?php

use Tests\BrowserKitTestCase;
use KBox\Documents\Preview\WordDocumentPreview;
use KBox\Documents\FileProperties;

class WordDocumentPreviewTest extends BrowserKitTestCase
{
    public function testConvertDocxToHtml()
    {
        $path = __DIR__.'/data/example.docx';

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
