<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Content\Preview\TextPreview;

class TextPreviewTest extends TestCase
{
    
    public function testConvertToHtml()
    {
        $path = __DIR__ . '/data/text.txt';

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
