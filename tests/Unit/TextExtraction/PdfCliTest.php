<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use KBox\Documents\Pdf\PdfCli;

class PdfCliTest extends TestCase
{
    protected function setUp()
    {
        if (empty(glob('./bin/pdftotext*'))) {
            $this->markTestSkipped(
                'PDF to text dependency not installed.'
            );
        }

        parent::setUp();
    }

    public function test_pdf_cli_can_extract_text()
    {
        $cli = new PdfCli();

        $text = $cli->convertToText(realpath(__DIR__.'/../../data/example.pdf'));

        $this->assertEquals('Example document for unit tests', $text);
    }
}
