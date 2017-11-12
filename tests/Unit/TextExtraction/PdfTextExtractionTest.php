<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use Content\ExtractText\PdfExtractor;

class PdfTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_pdf_file()
    {
        $extractor = new PdfExtractor();

        $file = realpath(__DIR__.'/../../data/example.pdf');

        $this->assertEquals('Example document for unit tests', $extractor->load($file)->text());
    }
}
