<?php

use Tests\BrowserKitTestCase;
use KBox\Documents\Preview\PresentationPreview;
use KBox\Documents\FileProperties;
use KBox\Documents\Presentation\PresentationProperties;

class PresentationPreviewTest extends BrowserKitTestCase
{
    public function testConvertPptxToHtml()
    {
        $path = __DIR__.'/data/presentation.pptx';

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
