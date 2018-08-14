<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\ExtractText\PdfExtractor;

class PdfTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_pdf_file()
    {
        Storage::fake('local'); // as the text extractor uses a stored cache location

        $extractor = new PdfExtractor();

        $file = realpath(__DIR__.'/../../data/example.pdf');

        $this->assertEquals('Example document for unit tests', $extractor->load($file)->text());
    }

    public function test_utf8_text_is_extracted_from_pdf_file()
    {
        Storage::fake('local'); // as the text extractor uses a stored cache location

        $extractor = new PdfExtractor();

        $file = realpath(__DIR__.'/../../data/unicodeexample.pdf');

        $extracted_text = $extractor->load($file)->text();

        // testing that something has been extracted
        $this->assertNotEmpty($extracted_text);
        $this->assertTrue(strlen($extracted_text) > 10);
    }
}
