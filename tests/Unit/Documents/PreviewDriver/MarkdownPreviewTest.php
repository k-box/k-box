<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\MarkdownPreview;
use Illuminate\Contracts\Support\Renderable;

class MarkdownPreviewTest extends TestCase
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
    
    public function test_markdown_can_be_previewed()
    {
        $path = base_path('tests/data/markdown.md');

        $preview = (new MarkdownPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(MarkdownPreview::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('<h1>This is</h1>', $html);
        $this->assertContains('a <strong>Markdown</strong> file', $html);
        $this->assertContains('preview__render preview__render--text', $html);
    }
}
