<?php

namespace Tests\Unit\Streaming;

use Tests\TestCase;
use KBox\Option;
use KBox\Facades\KlinkStreaming;
use Illuminate\Support\Facades\Storage;

class KlinkStreamingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(getenv('STREAMING_SERVICE_URL'))) {
            $this->markTestSkipped(
                'STREAMING_SERVICE_URL not configured for running integration tests.'
            );
        }

        // configuring the options for having a usable streaming client
        Option::put(Option::STREAMING_SERVICE_URL, getenv('STREAMING_SERVICE_URL'));
        Option::put(Option::PUBLIC_CORE_PASSWORD, 'A-token');
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
    }

    public function test_upload_to_streaming_service()
    {
        Storage::fake('local');

        $path = 'publishing/video.mp4';

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );

        $upload = KlinkStreaming::upload(Storage::disk('local')->path($path));

        $this->assertInstanceOf(\Oneofftech\KlinkStreaming\Upload::class, $upload);
        $this->assertNotEmpty($upload->videoId());
        $this->assertFalse($upload->isRunning());

        return $upload->videoId();
    }

    /**
     * @depends test_upload_to_streaming_service
     */
    public function test_streaming_video_can_be_retrieved($video_id)
    {
        $video = KlinkStreaming::get($video_id);

        $this->assertInstanceOf(\Oneofftech\KlinkStreaming\Video::class, $video);
        $this->assertEquals($video_id, $video->id);
        $this->assertNotEmpty($video->url);
        
        return $video_id;
    }

    /**
     * @depends test_streaming_video_can_be_retrieved
     */
    public function test_streaming_video_is_deleted($video_id)
    {
        $video = KlinkStreaming::delete($video_id);
        
        $this->assertInstanceOf(\Oneofftech\KlinkStreaming\Video::class, $video);
        $this->assertEquals($video_id, $video->id);
        $this->assertEquals('deleted', $video->status);
    }
}
