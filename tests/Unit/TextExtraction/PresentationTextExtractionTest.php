<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use KBox\Documents\ExtractText\PresentationExtractor;

class PresentationTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_pptx_file()
    {
        $extractor = new PresentationExtractor();

        $file = realpath(__DIR__.'/../../data/example-presentation-simple.pptx');

        $this->assertEquals(
            'Simple presentation With a subtitle A new titled page Some Bullet Points',
            $extractor->load($file)->text()
        );
    }
}
