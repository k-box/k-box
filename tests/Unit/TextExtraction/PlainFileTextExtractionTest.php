<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use Content\ExtractText\TextFileExtractor;

class PlainFileTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_plain_text_file()
    {
        $extractor = new TextFileExtractor();

        $file = realpath(__DIR__.'/../../data/example.txt');

        $this->assertEquals('A text file example for testing the full text search integration', $extractor->load($file)->text());
    }

    public function test_can_extract_text_from_markdown_file()
    {
        $extractor = new TextFileExtractor();

        $file = realpath(__DIR__.'/../../contentprocessing/data/markdown.md');

        $this->assertEquals(file_get_contents($file), $extractor->load($file)->text());
    }

    public function test_can_extract_text_from_csv_file()
    {
        $extractor = new TextFileExtractor();

        $file = realpath(__DIR__.'/../../contentprocessing/data/csv1.csv');

        $this->assertEquals(file_get_contents($file), $extractor->load($file)->text());
    }
}
