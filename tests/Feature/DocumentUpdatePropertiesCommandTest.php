<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Artisan;
use Illuminate\Support\Facades\Storage;

class DocumentUpdatePropertiesCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
    }

    public function test_that_document_update_command_updates_video_properties()
    {
        $this->withKlinkAdapterMock();

        $path = '2017/11/video.mp4';
        
        Storage::disk('local')->makeDirectory('2017/11/');

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        
        $file = factory(\KBox\File::class)->create([
            'path' => Storage::disk('local')->path($path),
            'mime_type' => 'video/mp4',
            'properties' => null,
        ]);

        Storage::disk('local')->assertExists("2017/11/video.mp4");

        $doc = factory(\KBox\DocumentDescriptor::class)->create([
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
