<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\SpreadsheetPreview;

class SpreadsheetPreviewTest extends TestCase
{
    use DatabaseTransactions;

    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return factory(File::class)->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }
    
    public function testConvertCsvToHtml()
    {
        $path = base_path('tests/data/csv1.csv');

        $preview = (new SpreadsheetPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(SpreadsheetPreview::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertContains('second', $html);
        $this->assertContains('line', $html);
        $this->assertContains('preview__render preview__render--spreadsheet', $html);
    }
    
    public function testConvertXslxToHtml()
    {
        $path = base_path('tests/data/spreadsheet.xlsx');

        $preview = (new SpreadsheetPreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(SpreadsheetPreview::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertContains('preview__render preview__render--spreadsheet', $html);
    }
}
