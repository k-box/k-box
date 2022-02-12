<?php

namespace KBox\Documents\Testing\Fakes;

use KBox\File;
use PHPUnit\Framework\Assert as PHPUnit;
use KBox\Documents\Services\ThumbnailsService;

class ThumbnailsServiceFake extends ThumbnailsService
{
    /**
     * All of the jobs that have been pushed.
     *
     * @var array
     */
    protected $jobs = [];
    
    /**
     * All the generation calls that have been issued
     */
    protected $generations = [];

    /**
     * Assert if a file thumbnail was generated.
     *
     * @param  \KBox\File  $file
     * @return void
     */
    public function assertGenerateCalled(File $file)
    {
        PHPUnit::assertTrue(
            $this->generated($file)->count() > 0,
            "The expected file [{$file->getKey()}] thumbnail was not generated."
        );
    }

    /**
     * Assert if a file was queued for thumbnail generation.
     *
     * @param  \KBox\File  $file
     * @return void
     */
    public function assertQueued(File $file)
    {
        PHPUnit::assertTrue(
            $this->queued($file)->count() > 0,
            "The expected file [{$file->getKey()}] was not queued for elaboration."
        );
    }

    /**
     * Determine if a job was pushed
     *
     * @param  File  $file
     * @return void
     */
    public function assertNotQueued(File $file)
    {
        PHPUnit::assertTrue(
            $this->queued($file)->count() === 0,
            "The unexpected document [{$file->getKey()}] elaboration was queued."
        );
    }

    /**
     * Assert that no jobs were pushed.
     *
     * @return void
     */
    public function assertNothingQueued()
    {
        PHPUnit::assertEmpty($this->jobs, 'DocumentElaborations were pushed unexpectedly.');
    }

    /**
     * Get all of the jobs matching a truth-test callback.
     *
     * @param  File  $file
     * @return \Illuminate\Support\Collection
     */
    public function queued(File $file)
    {
        if (! $this->isQueued($file)) {
            return collect();
        }

        return collect($this->jobs[$file->getKey()])->filter(function ($data) use ($file) {
            return $data['job']->is($file);
        })->pluck('job');
    }

    /**
     * Get all of the generations matching a truth-test callback.
     *
     * @param  File  $file
     * @return \Illuminate\Support\Collection
     */
    public function generated(File $file)
    {
        if (! (isset($this->generations[$file->getKey()]) && ! empty($this->generations[$file->getKey()]))) {
            return collect();
        }

        return collect($this->generations[$file->getKey()])->filter(function ($data) use ($file) {
            return $data['job']->is($file);
        })->pluck('job');
    }

    /**
     * Determine if there are any stored jobs for a given class.
     *
     * @param  string  $job
     * @return bool
     */
    public function isQueued($file)
    {
        return isset($this->jobs[$file->getKey()]) && ! empty($this->jobs[$file->getKey()]);
    }

    /**
     * @inherit
     */
    public function generate(File $file)
    {
        $this->generations[$file->getKey()][] = [
            'job' => $file,
        ];

        return $file;
    }
    
    /**
     * @inherit
     */
    public function queue(File $file)
    {
        $this->jobs[$file->getKey()][] = [
            'job' => $file,
            'queue' => $this->queue,
        ];
    }
}
