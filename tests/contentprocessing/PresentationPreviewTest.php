<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Content\Preview\PresentationPreview;
use Content\FileProperties;
use Content\Presentation\PresentationProperties;

class PresentationPreviewTest extends TestCase
{
    
    public function testConvertPptxToHtml()
    {
        $path = __DIR__ . '/data/presentation.pptx';

        $preview = (new PresentationPreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(PresentationPreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNotNull($preview->properties());
        $this->assertInstanceOf(FileProperties::class, $preview->properties());
        $this->assertInstanceOf(PresentationProperties::class, $preview->properties());
        $this->assertEquals(4, $preview->properties()->totalSlides());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('slides', $html);
    }
    
}
