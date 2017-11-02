<?php

namespace KlinkDMS\Jobs;

use Illuminate\Bus\Queueable;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Publication;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Klink\DmsDocuments\DocumentsService;
use Klink\DmsAdapter\KlinkVisibilityType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePublishedDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $document;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DocumentDescriptor $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DocumentsService $service)
    {
        \Log::info("Updating published document Job handling for {$this->document->uuid}");

        if ($this->document->hasPendingPublications() || ! $this->document->isPublished()) {
            return true;
        }
        
        $publication = $this->document->publication();
        
        try {
            $returned_descriptor = $service->updateDocumentProxy($this->document, $this->document->file, KlinkVisibilityType::KLINK_PUBLIC);

            $publication->touch();

            return true;
        } catch (\Exception $ex) {
            \Log::error('Exception during update existing document publication', ['publication' => $publication, 'document' => $this->document, 'error' => $ex]);

            $publication->failed = true;
            
            $publication->save();

            return false;
        }
    }
}
