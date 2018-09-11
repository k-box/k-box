<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\GoogleDrivePreview;
use Illuminate\Contracts\Support\Renderable;

class GoogleDrivePreviewTest extends TestCase
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

    public function test_preview_gdoc()
    {
        $path = __DIR__.'/data/example-file.gdoc';

        $preview = (new GoogleDrivePreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(GoogleDrivePreview::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertContains('https://docs.google.com/open?id=FAKE_ID', $html);
        $this->assertContains(trans('documents.preview.open_in_google_drive_btn'), $html);
        $this->assertContains(trans('documents.preview.google_file_disclaimer_alt'), $html);
        $this->assertContains('preview__render preview__render--googledrive', $html);
    }
    
    public function test_preview_gslides()
    {
        $path = __DIR__.'/data/example-presentation.gslides';

        $preview = (new GoogleDrivePreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(GoogleDrivePreview::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertContains('https://docs.google.com/open?id=FAKE_ID', $html);
        $this->assertContains(trans('documents.preview.open_in_google_drive_btn'), $html);
        $this->assertContains(trans('documents.preview.google_file_disclaimer_alt'), $html);
        $this->assertContains('preview__render preview__render--googledrive', $html);
    }
    
    public function test_preview_gsheet()
    {
        $path = __DIR__.'/data/example-spreadsheet.gsheet';

        $preview = (new GoogleDrivePreview())->preview($this->createFileForPath($path));
        $html = $preview->render();

        $this->assertInstanceOf(GoogleDrivePreview::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotEmpty($html);
        $this->assertContains('https://docs.google.com/open?id=FAKE_ID', $html);
        $this->assertContains(trans('documents.preview.open_in_google_drive_btn'), $html);
        $this->assertContains(trans('documents.preview.google_file_disclaimer_alt'), $html);
        $this->assertContains('preview__render preview__render--googledrive', $html);
    }
}
