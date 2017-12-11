<?php

namespace Tests\Unit\Streaming;

use Tests\TestCase;
use KBox\Facades\KlinkStreaming;
use KBox\Support\Testing\Fakes\KlinkStreamingClientFake;

class KlinkStreamingFakeTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->fake = new KlinkStreamingClientFake;
    }
    public function test_fake_client_is_instantiated_using_the_facade()
    {
        KlinkStreaming::fake();

        KlinkStreaming::uploaded('nothing.mp4');
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage The expected [video.mp4] video was not uploaded.
     */
    public function test_assert_video_was_uploaded()
    {
        $this->fake->assertUploaded('video.mp4');
        
        $this->fake->upload('video.mp4');

        $this->fake->assertUploaded('video.mp4');
    }
    
    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage The unexpected [video.mp4] video was uploaded.
     */
    public function test_assert_video_was_not_uploaded()
    {
        $this->fake->assertNotUploaded('video-not-uploaded.mp4');

        $this->fake->upload('video.mp4');

        $this->fake->assertNotUploaded('video.mp4');
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Videos were uploaded unexpectedly.
     */
    public function test_assert_nothing_uploaded()
    {
        $this->fake->assertNothingUploaded();

        $this->fake->upload('video.mp4');

        $this->fake->assertNothingUploaded();
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage The expected [abcde123] video was not deleted.
     */
    public function test_assert_video_was_deleted()
    {
        $this->fake->assertDeleted('abcde123');
        
        $this->fake->delete('abcde123');

        $this->fake->assertDeleted('abcde123');
    }
    
    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage The unexpected [abcde123] video was deleted.
     */
    public function test_assert_video_was_not_deleted()
    {
        $this->fake->assertNotDeleted('abcde123');

        $this->fake->delete('abcde123');

        $this->fake->assertNotDeleted('abcde123');
    }
}
