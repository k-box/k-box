<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use KBox\Documents\ExtractText\WordExtractor;

class WordTextExtractionTest extends TestCase
{
    public function test_can_extract_text_from_docx_file()
    {
        $extractor = new WordExtractor();

        $file = realpath(__DIR__.'/../../data/example.docx');

        $this->assertEquals('Example document for unit tests', $extractor->load($file)->text());
    }
    
    public function test_can_extract_text_from_multi_line_docx_file()
    {
        $extractor = new WordExtractor();

        $file = realpath(__DIR__.'/../../data/example-with-multiline.docx');

        $this->assertEquals('This document contains multi line and paragraphs with bold and italic', $extractor->load($file)->text());
    }
}
