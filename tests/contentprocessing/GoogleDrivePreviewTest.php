<?php

use Tests\BrowserKitTestCase;
use Content\Preview\GoogleDrivePreview;

class GoogleDrivePreviewTest extends BrowserKitTestCase
{
    public function testConvertGDocToHtml()
    {
        $path = __DIR__.'/data/example-file.gdoc';

        $preview = (new GoogleDrivePreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(GoogleDrivePreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNull($preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('https://docs.google.com/open?id=FAKE_ID', $html);
        $this->assertContains(trans('documents.preview.open_in_google_drive_btn'), $html);
        $this->assertContains(trans('documents.preview.google_file_disclaimer_alt'), $html);
        $this->assertContains('preview__render preview__render--googledrive', $html);
    }
    
    public function testConvertGSlidesToHtml()
    {
        $path = __DIR__.'/data/example-presentation.gslides';

        $preview = (new GoogleDrivePreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(GoogleDrivePreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNull($preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('https://docs.google.com/open?id=FAKE_ID', $html);
        $this->assertContains(trans('documents.preview.open_in_google_drive_btn'), $html);
        $this->assertContains(trans('documents.preview.google_file_disclaimer_alt'), $html);
        $this->assertContains('preview__render preview__render--googledrive', $html);
    }
    
    public function testConvertGSheetToHtml()
    {
        $path = __DIR__.'/data/example-spreadsheet.gsheet';

        $preview = (new GoogleDrivePreview())->load($path);
        $html = $preview->html();

        $this->assertInstanceOf(GoogleDrivePreview::class, $preview);
        $this->assertNull($preview->css());
        $this->assertNull($preview->properties());
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertContains('https://docs.google.com/open?id=FAKE_ID', $html);
        $this->assertContains(trans('documents.preview.open_in_google_drive_btn'), $html);
        $this->assertContains(trans('documents.preview.google_file_disclaimer_alt'), $html);
        $this->assertContains('preview__render preview__render--googledrive', $html);
    }
}
