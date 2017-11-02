<?php

namespace KlinkDMS\Jobs;

use Illuminate\Bus\Queueable;
use KlinkDMS\Publication;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Klink\DmsDocuments\DocumentsService;
use Klink\DmsAdapter\KlinkVisibilityType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $publication;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DocumentsService $service)
    {
        $document = $this->publication->document;

        \Log::info("Publish Job handling for {$this->publication->id}: $document->uuid");

        if ($this->publication->published && $publication->hasPendingPublications()) {
            // Abort as publication is already happened or is in the process
            return true;
        }
        
        try {
            $returned_descriptor = $service->updateDocumentProxy($document, $document->file, KlinkVisibilityType::KLINK_PUBLIC);

            $this->publication->published = true;

            $this->publication->save();

            return true;
        } catch (\Exception $ex) {
            \Log::error('Exception during document publishing', ['publication' => $this->publication, 'document' => $document, 'error' => $ex]);

            $this->publication->failed = true;
            
            $this->publication->save();

            return false;
        }
    }
}
