<?php

namespace KBox\Jobs;

use KBox\DocumentDescriptor;
use KBox\Documents\Services\DocumentsService;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Reindex a single document
 *
 * It runs on the queue.
 *
 * @uses \KBox\Documents\Services\DocumentsService
 */
class ReindexDocument extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $document = null;
    
    private $user = null;

    public $visibility = null;

    /**
     * Create a new reindex document job.
     *
     * @param DocumentDescriptor $document   The document
     * @param string             $visibility The visibility of the document that needs to be updated. Can be "public" or "private"
     */
    public function __construct(DocumentDescriptor $document, $visibility)
    {
        $this->document = $document;
        $this->visibility = $visibility;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(DocumentsService $service)
    {
        \Log::info('Reindex Job handling for '.$this->document->uuid, ['document' => $this->document, 'visibility' => $this->visibility]);

        try {
            $service->reindexDocument($this->document, $this->visibility, false);

            return true;
        } catch (\Exception $ex) {
            \Log::error('Exception during document reindex', ['document' => $this->document, 'visibility' => $this->visibility, 'error' => $ex]);

            return false;
        }
    }
}
