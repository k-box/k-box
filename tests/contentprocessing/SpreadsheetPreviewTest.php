<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Content\Preview\SpreadsheetPreview;
use Content\FileProperties;

class SpreadsheetPreviewTest extends TestCase
{
    
    public function testConvertCsvToHtml()
    {
        $path = __DIR__ . '/data/csv1.csv';

        $preview = (new SpreadsheetPreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(SpreadsheetPreview::class, $preview);
        $this->assertNotNull($preview->css());
        $this->assertNotNull($preview->properties());
        $this->assertInstanceOf(FileProperties::class, $preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('second', $html);
        $this->assertContains('line', $html);
        $this->assertContains('preview__render preview__render--spreadsheet', $html);
    }
    
    public function testConvertXslxToHtml()
    {
        $path = __DIR__ . '/data/spreadsheet.xlsx';

        $preview = (new SpreadsheetPreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(SpreadsheetPreview::class, $preview);
        $this->assertNotNull($preview->css());
        $this->assertNotNull($preview->properties());
        $this->assertInstanceOf(FileProperties::class, $preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('preview__render preview__render--spreadsheet', $html);
    }
    
}
