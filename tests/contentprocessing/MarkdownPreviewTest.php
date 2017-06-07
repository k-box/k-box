<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Content\Preview\MarkdownPreview;

class MarkdownPreviewTest extends TestCase
{
    
    public function testConvertToHtml()
    {
        $path = __DIR__ . '/data/markdown.md';

        $preview = (new MarkdownPreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(MarkdownPreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNull($preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('<h1>This is</h1>', $html);
        $this->assertContains('a <strong>Markdown</strong> file', $html);
        $this->assertContains('preview__render preview__render--text', $html);
    }
    
}
