<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\DocumentDescriptor;
use KBox\Documents\FileHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Documents\Preview\ImagePreviewDriver;
use Illuminate\Contracts\Support\Renderable;

class ImagePreviewDriverTest extends TestCase
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
    
    public function test_jpg_can_be_previewed()
    {
        $path = base_path('tests/data/example.jpg');

        $file = $this->createFileForPath($path);

        $document = factory(DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'mime_type' => $file->mime_type
        ]);

        $preview = (new ImagePreviewDriver())->preview($file);
        $preview->with(['document' => $document]);
        $html = $preview->render();

        $this->assertInstanceOf(ImagePreviewDriver::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('img src', $html);
        $this->assertStringContainsString($document->uuid, $html);
        $this->assertStringContainsString($file->uuid, $html);
    }
    
    public function test_gif_can_be_previewed()
    {
        $path = base_path('tests/data/example.gif');

        $file = $this->createFileForPath($path);

        $document = factory(DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'mime_type' => $file->mime_type
        ]);

        $preview = (new ImagePreviewDriver())->preview($file);
        $preview->with(['document' => $document]);
        $html = $preview->render();

        $this->assertInstanceOf(ImagePreviewDriver::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('img src', $html);
        $this->assertStringContainsString($document->uuid, $html);
        $this->assertStringContainsString($file->uuid, $html);
    }
    
    public function test_png_can_be_previewed()
    {
        $path = base_path('tests/data/project-avatar.png');

        $file = $this->createFileForPath($path);

        $document = factory(DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'mime_type' => $file->mime_type
        ]);

        $preview = (new ImagePreviewDriver())->preview($file);
        $preview->with(['document' => $document]);
        $html = $preview->render();

        $this->assertInstanceOf(ImagePreviewDriver::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('img src', $html);
        $this->assertStringContainsString($document->uuid, $html);
        $this->assertStringContainsString($file->uuid, $html);
    }
}
