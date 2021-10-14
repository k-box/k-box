<?php

namespace Tests\Unit;

use KBox\File;
use KBox\User;
use Tests\TestCase;
use KBox\Jobs\ConvertVideo;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Storage;
use Mockery;

class ConvertVideoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(glob(base_path('bin/video-processing-cli*')))) {
            $this->markTestSkipped(
                'The video processing cli is not installed.'
            );
        }
    }

    public function test_non_mp4_file_is_discarded()
    {
        // generate a mock of the VideoProcessorFactory::class to confirm that make() is not called
        
        $mock = Mockery::mock('\OneOffTech\VideoProcessing\VideoProcessorFactory');

        $this->swap('OneOffTech\VideoProcessing\VideoProcessorFactory', $mock);

        $mock->shouldReceive('make')->never();

        Storage::fake('local');

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create();

        dispatch(new ConvertVideo($descriptor));

        // Storage::disk('local')->assertMissing('');
    }

    public function test_mp4_file_is_processed()
    {
        Storage::fake('local');

        $uuid = (new File)->resolveUuid()->toString();

        $folder = "2017/08/$uuid";

        Storage::disk('local')->makeDirectory($folder);

        $file_path = "$folder/AVIDEO.mp4";

        // copy a test file to the file storage
        Storage::disk('local')->put($file_path, file_get_contents(base_path('tests/data/video.mp4')));

        $user_id = factory(User::class)->create()->id;

        $file = factory(File::class)->create([
            'name' => "AVIDEO.mp4",
            'hash' => hash_file('sha512', Storage::disk('local')->path($file_path)),
            'path' => $file_path,
            'mime_type' => 'video/mp4',
            'user_id' => $user_id,
            'size' =>  Storage::disk('local')->size($file_path),
            'original_uri' => '',
            'upload_completed_at' => \Carbon\Carbon::now()
        ]);

        $descriptor = factory(DocumentDescriptor::class)->create([
            'local_document_id' => substr($file->hash, 0, 6),
            'title' => "AVIDEO.mp4",
            'hash' => $file->hash,
            'document_uri' => 'http://localhost/1/document',
            'thumbnail_uri' => 'http://localhost/1/thumbnail',
            'mime_type' => 'video/mp4',
            'visibility' => 'private',
            'document_type' => 'document',
            'user_owner' => 'some user <usr@user.com>',
            'user_uploader' => 'some user <usr@user.com>',
            'abstract' => '',
            'language' => 'en',
            'file_id' => $file->id,
            'owner_id' => $user_id,
            'status' => DocumentDescriptor::STATUS_PROCESSING,
        ]);

        dispatch(new ConvertVideo($descriptor));

        // in this current test the video height is less
        // than 540 pixels, so we expects only 3 files in
        // the folder (plus the original file, of course)

        Storage::disk('local')->assertExists($file_path);
        Storage::disk('local')->assertExists("$folder/AVIDEO-360_audio.mp4");
        Storage::disk('local')->assertExists("$folder/AVIDEO-360_video.mp4");
        Storage::disk('local')->assertExists("$folder/AVIDEO.mpd");
    }
}
