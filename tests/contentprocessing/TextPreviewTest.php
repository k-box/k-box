<?php

use Tests\BrowserKitTestCase;
use KBox\Documents\Preview\TextPreview;

class TextPreviewTest extends BrowserKitTestCase
{
    public function testConvertToHtml()
    {
        $path = __DIR__.'/data/text.txt';

        $preview = (new TextPreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(TextPreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNull($preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('TXT file', $html);
        $this->assertContains('<br/>with a new line', $html);
        $this->assertContains('preview__render preview__render--text', $html);
    }
}
