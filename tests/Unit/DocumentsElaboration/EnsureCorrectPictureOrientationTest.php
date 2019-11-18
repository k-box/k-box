<?php

namespace Tests\Unit\DocumentsElaboration;

use KBox\File;
use Tests\TestCase;
use KBox\DocumentDescriptor;
use KBox\Documents\FileHelper;
use Intervention\Image\Facades\Image as ImageFacade;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use KBox\DocumentsElaboration\Actions\EnsureCorrectPictureOrientation;

class EnsureCorrectPictureOrientationTest extends TestCase
{
    use DatabaseTransactions;

    private function createImageDescriptor($test_file)
    {
        Storage::fake('local');

        Storage::disk('local')->put(basename($test_file), file_get_contents($test_file));

        list($mimeType) = FileHelper::type($test_file);

        $file = factory(File::class)->create([
            'path' => Storage::disk('local')->path(basename($test_file)),
            'mime_type' => $mimeType,
            'hash' => hash_file('sha512', $test_file)
        ]);

        return factory(DocumentDescriptor::class)->create([
            'file_id' => $file->getKey(),
            'mime_type' => $file->mime_type,
            'document_type' => 'image',
        ]);
    }

    public function test_jpeg_with_orientation_is_rotated()
    {
        $descriptor = $this->createImageDescriptor(__DIR__.'/../../data/example-vertical.jpg');

        $image = ImageFacade::make($descriptor->file->absolute_path);

        $action = new EnsureCorrectPictureOrientation();

        $returned_descriptor = $action->run($descriptor)->fresh();

        $after_image = ImageFacade::make($returned_descriptor->file->absolute_path);

        $this->assertTrue($descriptor->is($returned_descriptor));
        $this->assertFalse($descriptor->file->is($returned_descriptor->file));
        $this->assertNotEquals($descriptor->file->hash, $returned_descriptor->file->hash);
        $this->assertEquals($after_image->height(), $image->width());
        $this->assertEquals($after_image->width(), $image->height());
        $this->assertNull($after_image->exif('Orientation'));
    }

    public function test_png_is_ignored()
    {
        $descriptor = $this->createImageDescriptor(__DIR__.'/../../data/project-avatar.png');

        $image = ImageFacade::make($descriptor->file->absolute_path);

        $action = new EnsureCorrectPictureOrientation();

        $returned_descriptor = $action->run($descriptor);

        $after_image = ImageFacade::make($returned_descriptor->file->absolute_path);

        $this->assertTrue($descriptor->is($returned_descriptor));
        $this->assertEquals($descriptor->file->hash, $returned_descriptor->file->fresh()->hash);
        $this->assertEquals($after_image->height(), $image->height());
        $this->assertEquals($after_image->width(), $image->width());
    }

    public function test_gif_is_ignored()
    {
        $descriptor = $this->createImageDescriptor(__DIR__.'/../../data/example.gif');

        $image = ImageFacade::make($descriptor->file->absolute_path);

        $action = new EnsureCorrectPictureOrientation();

        $returned_descriptor = $action->run($descriptor);

        $after_image = ImageFacade::make($returned_descriptor->file->absolute_path);

        $this->assertTrue($descriptor->is($returned_descriptor));
        $this->assertEquals($descriptor->file->hash, $returned_descriptor->file->fresh()->hash);
        $this->assertEquals($after_image->height(), $image->height());
        $this->assertEquals($after_image->width(), $image->width());
    }
}
