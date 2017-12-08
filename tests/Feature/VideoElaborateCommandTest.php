<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Artisan;
use Illuminate\Support\Facades\Storage;

class VideoElaborateCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
    }

    public function test_video_elaborate_command_process_a_document()
    {
        $this->withKlinkAdapterMock();

        $path = '2017/11/video.mp4';
        
        Storage::disk('local')->makeDirectory('2017/11/');

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        
        $file = factory('KlinkDMS\File')->create([
            'path' => Storage::disk('local')->path($path),
            'mime_type' => 'video/mp4',
            'properties' => null,
        ]);

        Storage::disk('local')->assertExists("2017/11/video.mp4");

        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            "mime_type" => 'video/mp4',
            'file_id' => $file->id
        ]);
        
        $exitCode = Artisan::call('video:elaborate', [
            'documents' => $doc->id,
        ]);
        
        $this->assertEquals(0, $exitCode);

        $resources = $file->fresh()->videoResources();

        $this->assertNotEmpty($resources);
        $this->assertNotNull($resources->get('dash'));
        $this->assertNotNull($resources->get('streams'));
        $this->assertNotEmpty($resources->get('streams'));
        $this->assertEquals(2, $resources->get('streams')->count());

        Storage::disk('local')->assertExists("2017/11/video.mpd");
        Storage::disk('local')->assertExists("2017/11/video-360_video.mp4");
        Storage::disk('local')->assertExists("2017/11/video-360_audio.mp4");
    }
}
