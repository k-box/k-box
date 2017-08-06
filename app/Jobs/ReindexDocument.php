<?php

namespace KlinkDMS\Jobs;

use KlinkDMS\User;
use KlinkDMS\DocumentDescriptor;
use Klink\DmsDocuments\DocumentsService;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Reindex a single document
 *
 * It runs on the queue.
 *
 * @uses \Klink\DmsDocuments\DocumentsService
 */
class ReindexDocument extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $document = null;
    
    private $user = null;

    private $visibility = null;

    /**
     * Create a new reindex document job.
     *
     * @param User               $user       The user that have perfomed the action
     * @param DocumentDescriptor $document   The document
     * @param string             $visibility The visibility of the document that needs to be updated. Can be "public" or "private"
     */
    public function __construct(User $user, DocumentDescriptor $document, $visibility)
    {
        $this->document = $document;
        $this->user = $user;
        $this->visibility = $visibility;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(DocumentsService $service)
    {
        \Log::info('Reindex document', ['document' => $this->document, 'visibility' => $this->visibility, 'user' => $this->user]);

        try {
            $service->reindexDocument($this->document, $this->visibility, false);

            return true;
        } catch (\Exception $ex) {
            \Log::error('Exception during reindex document', ['document' => $this->document, 'visibility' => $this->visibility, 'user' => $this->user, 'error' => $ex]);

            return false;
        }
    }
}
