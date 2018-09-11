<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use KBox\Documents\ExtractText\TextFileExtractor;

class PlainFileTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_plain_text_file()
    {
        $extractor = new TextFileExtractor();

        $file = base_path('tests/data/example.txt');

        $this->assertEquals('A text file example for testing the full text search integration', $extractor->load($file)->text());
    }

    public function test_can_extract_text_from_markdown_file()
    {
        $extractor = new TextFileExtractor();

        $file = base_path('tests/data/markdown.md');

        $this->assertEquals(file_get_contents($file), $extractor->load($file)->text());
    }

    public function test_can_extract_text_from_csv_file()
    {
        $extractor = new TextFileExtractor();

        $file = base_path('tests/data/csv1.csv');

        $this->assertEquals(file_get_contents($file), $extractor->load($file)->text());
    }
}
