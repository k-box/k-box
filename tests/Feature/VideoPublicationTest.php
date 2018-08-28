<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use KBox\Publication;
use KBox\Option;
use KBox\Facades\KlinkStreaming;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Facades\Files;
use KBox\Jobs\UpdatePublishedDocumentJob;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VideoPublicationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();

        Storage::fake('local');

        // setting network connection options
        Option::put(Option::STREAMING_SERVICE_URL, 'https://streaming.service/');
        Option::put(Option::PUBLIC_CORE_URL, 'https://network.service/');
        Option::put(Option::PUBLIC_CORE_PASSWORD, 'A-token');
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
    }

    private function createVideo()
    {
        $path = 'publishing/video.mp4';
        
        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );

        $user = factory(\KBox\User::class)->create();
        list($mime) = Files::recognize($path);
        $file = factory(\KBox\File::class)->create([
            'hash' => hash_file('sha512', Storage::disk('local')->path($path)),
            'path' => $path,
            'mime_type' => $mime,
        ]);

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'language' => null,
            'abstract' => null,
        ]);

        return $descriptor;
    }
    
    private function createPublishedVideo()
    {
        $path = 'publishing/video.mp4';
        
        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        list($mime) = Files::recognize($path);
        $file = factory(\KBox\File::class)->create([
            'hash' => hash_file('sha512', Storage::disk('local')->path($path)),
            'path' => $path,
            'created_at' => Carbon::now()->subMinutes(2),
            'updated_at' => Carbon::now()->subMinutes(2),
            'mime_type' => $mime,
        ]);

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'language' => null,
            'abstract' => null,
            'is_public' => true,
        ]);

        $descriptor->publications()->create([
            'pending' => false,
            'published_at' => Carbon::now()->subMinutes(2),
            'streaming_url' => 'https://streaming.service/play/12345',
            'streaming_id' => '12345',
        ]);

        return $descriptor->fresh();
    }

    public function test_video_is_published_with_streaming_url()
    {
        $this->withKlinkAdapterFake();

        KlinkStreaming::fake();

        $descriptor = $this->createVideo();
        
        $user = factory(\KBox\User::class)->create();

        $descriptor->publish($user);

        $saved_descriptor = $descriptor->fresh();

        $publication = $saved_descriptor->publication();

        KlinkStreaming::assertUploaded($descriptor->file->absolute_path);
        
        $this->assertNotNull($publication);
        $this->assertNotNull($publication->published_at);
        $this->assertNotNull($publication->streaming_url);
        $this->assertNotNull($publication->streaming_id);
        $this->assertFalse($publication->pending);
        $this->assertEquals(Publication::STATUS_PUBLISHED, $publication->status);
    }

    public function test_publication_update_is_not_sending_video_if_file_was_not_changed()
    {
        $adapter = $this->withKlinkAdapterFake();
        
        KlinkStreaming::fake();

        $descriptor = $this->createPublishedVideo();
        
        $publication = $descriptor->publication();
        
        $published_at = $publication->updated_at;

        $descriptor->abstract = "New abstract";

        $descriptor->save();

        dispatch(new UpdatePublishedDocumentJob($descriptor->fresh()));

        $adapter->assertDocumentIndexed($descriptor->uuid);

        KlinkStreaming::assertNotUploaded($descriptor->file->absolute_path);

        $fresh_publication = $publication->fresh();
        $publication_updated_at = $fresh_publication->updated_at;
        
        $this->assertTrue($fresh_publication->updated_at->gt($fresh_publication->published_at));
        $this->assertEquals($publication->streaming_id, $fresh_publication->streaming_id);
    }

    public function test_publication_update_sends_updated_video_to_streaming_service()
    {
        $this->withKlinkAdapterFake();
        
        KlinkStreaming::fake();
        
        $descriptor = $this->createPublishedVideo();

        $path = 'publishing/updated-video.mp4';
        
        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        
        $original_publication = $descriptor->publication;

        // ADD a new file revision
        list($mime) = Files::recognize($path);
        $file = factory(\KBox\File::class)->create([
            'hash' => hash_file('sha512', Storage::disk('local')->path($path)),
            'path' => $path,
            'mime_type' => $mime,
            'revision_of' => $descriptor->file_id
        ]);

        $descriptor->file_id = $file->id;
        $descriptor->hash = $file->hash;

        $descriptor->save();

        dispatch(new UpdatePublishedDocumentJob($descriptor->fresh()));
        
        KlinkStreaming::assertDeleted($original_publication->streaming_id);
        KlinkStreaming::assertUploaded(Storage::disk('local')->path($path));

        $fresh_publication = $original_publication->fresh();

        $this->assertNotEquals($original_publication->streaming_id, $fresh_publication->streaming_id);
        $this->assertNotEquals($original_publication->streaming_url, $fresh_publication->streaming_url);
    }

    public function test_unpublish_removes_publication_and_video()
    {
        $this->withKlinkAdapterFake();
        
        KlinkStreaming::fake();
        
        $descriptor = $this->createPublishedVideo();
        $original_publication = $descriptor->publication;

        $user = factory(\KBox\User::class)->create();

        $descriptor->unpublish($user);
        
        KlinkStreaming::assertDeleted($original_publication->streaming_id);

        $fresh_publication = $original_publication->fresh();

        $this->assertNull($fresh_publication->streaming_url);
        $this->assertNull($fresh_publication->streaming_id);
    }
}
