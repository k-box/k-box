<?php

namespace KBox\DocumentsElaboration\Testing\Fakes;

use KBox\DocumentDescriptor;
use Illuminate\Contracts\Queue\Queue;
use PHPUnit\Framework\Assert as PHPUnit;
use KBox\DocumentsElaboration\DocumentElaborationManager;

class DocumentElaborationFake extends DocumentElaborationManager
{
    /**
     * All of the jobs that have been pushed.
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * Assert if a document was queued for elaboration.
     *
     * @param  \KBox\DocumentDescriptor  $document
     * @return void
     */
    public function assertQueued(DocumentDescriptor $document)
    {
        PHPUnit::assertTrue(
            $this->queued($document)->count() > 0,
            "The expected document [{$document->getKey()}] was not queued for elaboration."
        );
    }

    /**
     * Determine if a job was pushed based on a truth-test callback.
     *
     * @param  string  $job
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotQueued(DocumentDescriptor $document)
    {
        PHPUnit::assertTrue(
            $this->queued($document)->count() === 0,
            "The unexpected document [{$document->getKey()}] elaboration was queued."
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
     * @param  string  $job
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function queued(DocumentDescriptor $document)
    {
        if (! $this->isQueued($document)) {
            return collect();
        }

        return collect($this->jobs[$document->getKey()])->filter(function ($data) use ($document) {
            return $data['job']->is($document);
        })->pluck('job');
    }

    /**
     * Determine if there are any stored jobs for a given class.
     *
     * @param  string  $job
     * @return bool
     */
    public function isQueued($document)
    {
        return isset($this->jobs[$document->getKey()]) && ! empty($this->jobs[$document->getKey()]);
    }

    /**
     * @inherit
     */
    public function elaborate(DocumentDescriptor $descriptor)
    {
        //
    }
    
    /**
     * @inherit
     */
    public function queue(DocumentDescriptor $descriptor)
    {
        $this->jobs[$descriptor->getKey()][] = [
            'job' => $descriptor,
            'queue' => $this->queue,
        ];
    }
}
