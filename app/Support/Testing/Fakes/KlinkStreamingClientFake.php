<?php

namespace KBox\Support\Testing\Fakes;

use Oneofftech\KlinkStreaming\Video;
use PHPUnit\Framework\Assert as PHPUnit;
use Oneofftech\KlinkStreaming\Contracts\Client as ClientContract;

class KlinkStreamingClientFake implements ClientContract
{
    /**
     * All of the videos that have been uploaded.
     *
     * @var array
     */
    protected $videos = [];
    
    protected $deleted_videos = [];

    /**
     * Assert if a video file was uploaded based on a truth-test callback.
     *
     * @param  string  $file
     * @param  callable|null  $callback
     * @return void
     */
    public function assertUploaded($file, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->uploaded($file, $callback)->count() > 0,
            "The expected [{$file}] video was not uploaded."
        );
    }

    /**
     * Determine if a video file was not uploaded based on a truth-test callback.
     *
     * @param  string  $file
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotUploaded($file, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->uploaded($file, $callback)->count() === 0,
            "The unexpected [{$file}] video was uploaded."
        );
    }

    /**
     * Assert that no videos were uploaded.
     *
     * @return void
     */
    public function assertNothingUploaded()
    {
        PHPUnit::assertEmpty($this->videos, 'Videos were uploaded unexpectedly.');
    }

    /**
     * Assert if a video file was deleted based on a truth-test callback.
     *
     * @param  string  $video_id
     * @param  callable|null  $callback
     * @return void
     */
    public function assertDeleted($video_id, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->deleted($video_id, $callback)->count() > 0,
            "The expected [{$video_id}] video was not deleted."
        );
    }

    /**
     * Determine if a video file was not deleted based on a truth-test callback.
     *
     * @param  string  $video_id
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotDeleted($video_id, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->deleted($video_id, $callback)->count() === 0,
            "The unexpected [{$video_id}] video was deleted."
        );
    }

    /**
     * Get all of the videos matching a truth-test callback.
     *
     * @param  string  $file
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function uploaded($file, $callback = null)
    {
        if (! $this->wasUploaded($file)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return $this->uploadsOf($file)->filter(function ($file) use ($callback) {
            return $callback($file);
        });
    }
    
    /**
     * Get all of the deleted videos matching a truth-test callback.
     *
     * @param  string  $video_id
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function deleted($video_id, $callback = null)
    {
        if (! $this->wasDeleted($video_id)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return $this->deletionOf($video_id)->filter(function ($video_id) use ($callback) {
            return $callback($video_id);
        });
    }

    /**
     * Determine if the given video has been uploaded.
     *
     * @param  string  $file
     * @return bool
     */
    public function wasUploaded($file)
    {
        return $this->uploadsOf($file)->count() > 0;
    }

    /**
     * Determine if the given video has been deleted.
     *
     * @param  string  $video_id
     * @return bool
     */
    public function wasDeleted($video_id)
    {
        return $this->deletionOf($video_id)->count() > 0;
    }

    /**
     * Get all of the uploaded videos for a given file.
     *
     * @param  string  $file
     * @return \Illuminate\Support\Collection
     */
    protected function uploadsOf($file)
    {
        return collect($this->videos)->filter(function ($video) use ($file) {
            return $video === $file;
        });
    }
    
    /**
     * Get all of the deleted entries for a given video identifier.
     *
     * @param  string  $video_id
     * @return \Illuminate\Support\Collection
     */
    protected function deletionOf($video_id)
    {
        return collect($this->deleted_videos)->filter(function ($video) use ($video_id) {
            return $video === $video_id;
        });
    }

    /**
     * Upload a new video file.
     *
     * @param  string  $video path to video
     * @return \Oneofftech\KlinkStreaming\Upload
     */
    public function upload($video)
    {
        $this->videos[] = $video;

        return new KlinkStreamingUploadFake([
            'video_id' => '1234567890',
            'status' => 'queued',
            'created_at' => null,
            'updated_at' => null,
            'title' => null,
            'fail_reason' => null,
            'poster' => null,
            'dash_stream' => null,
            'url' => 'https://streaming.service/play/1234567890',
        ]);
    }
    
    /**
     * Upload a new video file.
     *
     * @param  string  $video path to video
     * @return \Oneofftech\KlinkStreaming\Upload
     */
    public function add($video)
    {
        return $this->upload($video);
    }

    /**
     *
     * @return \Oneofftech\KlinkStreaming\Video
     */
    public function get($video_id)
    {
        return new Video([
            'video_id' => $video_id,
            'status' => 'processing',
            'created_at' => null,
            'updated_at' => null,
            'title' => null,
            'fail_reason' => null,
            'poster' => null,
            'dash_stream' => null,
            'url' => "https://streaming.service/play/$video_id",
        ]);
    }

    /**
     *
     * @return \Oneofftech\KlinkStreaming\Video
     */
    public function delete($video_id)
    {
        $this->deleted_videos[] = $video_id;

        return new Video([
            'video_id' => $video_id,
            'status' => 'deleted',
            'created_at' => null,
            'updated_at' => null,
            'title' => null,
            'fail_reason' => null,
            'poster' => null,
            'dash_stream' => null,
            'url' => "https://streaming.service/play/$video_id",
        ]);
    }
}

class KlinkStreamingUploadFake extends Video
{
    public function videoId()
    {
        return $this->id;
    }
}
