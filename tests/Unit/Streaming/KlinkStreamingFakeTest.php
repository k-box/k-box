<?php

namespace Tests\Unit\Streaming;

use Tests\TestCase;
use KBox\Facades\KlinkStreaming;
use KBox\Support\Testing\Fakes\KlinkStreamingClientFake;
use Oneofftech\KlinkStreaming\Client;
use PHPUnit\Framework\ExpectationFailedException;

class KlinkStreamingFakeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fake = new KlinkStreamingClientFake;
    }
    public function test_fake_client_is_instantiated_using_the_facade()
    {
        KlinkStreaming::fake();

        $this->assertInstanceOf(KlinkStreamingClientFake::class, app()->make(Client::class));
    }

    public function test_assert_video_was_uploaded()
    {
        $this->fake->upload('video.mp4');

        $this->fake->assertUploaded('video.mp4');
    }
    
    public function test_assert_video_was_not_uploaded()
    {
        $this->fake->assertNotUploaded('video-not-uploaded.mp4');

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The unexpected [video.mp4] video was uploaded.");

        $this->fake->upload('video.mp4');

        $this->fake->assertNotUploaded('video.mp4');
    }

    public function test_assert_nothing_uploaded()
    {
        $this->fake->assertNothingUploaded();

        $this->fake->upload('video.mp4');

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Videos were uploaded unexpectedly.");

        $this->fake->assertNothingUploaded();
    }

    public function test_assert_video_was_deleted()
    {
        $this->fake->delete('abcde123');

        $this->fake->assertDeleted('abcde123');
    }
    
    public function test_assert_video_was_not_deleted()
    {
        $this->fake->assertNotDeleted('abcde123');

        $this->fake->delete('abcde123');

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The unexpected [abcde123] video was deleted.");

        $this->fake->assertNotDeleted('abcde123');
    }
}
