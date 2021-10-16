<?php

namespace Tests\Feature;

use Tests\TestCase;

use Artisan;
use Illuminate\Support\Facades\Storage;
use KBox\User;
use KBox\Project;
use KBox\DocumentDescriptor;
use KBox\File;

class DocumentUpdatePropertiesCommandTest extends TestCase
{
    public function test_that_document_update_command_updates_video_properties()
    {
        $this->withKlinkAdapterMock();

        $path = '2017/11/video.mp4';
        
        Storage::disk('local')->makeDirectory('2017/11/');

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        
        $file = File::factory()->create([
            'path' => Storage::disk('local')->path($path),
            'mime_type' => 'video/mp4',
            'properties' => null,
        ]);

        Storage::disk('local')->assertExists("2017/11/video.mp4");

        $doc = DocumentDescriptor::factory()->create([
            "mime_type" => 'video/mp4',
            'file_id' => $file->id
        ]);
        
        $exitCode = Artisan::call('document:properties-update', [
            'documents' => $doc->id,
        ]);
        
        $this->assertEquals(0, $exitCode);

        $file_after_processing = $file->fresh();

        $this->assertNotNull($file_after_processing->properties);
        $this->assertEquals('0:00:25.284000', $file_after_processing->properties->get('duration'));
    }
}
