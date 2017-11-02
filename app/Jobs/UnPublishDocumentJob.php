<?php

namespace KlinkDMS\Jobs;

use Illuminate\Bus\Queueable;
use KlinkDMS\Publication;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Contracts\KlinkAdapter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UnPublishDocumentJob implements ShouldQueue
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
    public function handle(KlinkAdapter $service)
    {
        $document = $this->publication->document;

        \Log::info("Un-Publish Job handling for {$this->publication->id}: $document->uuid");

        if ($this->publication->unpublished && $publication->hasPendingPublications()) {
            // Abort as unpublish is already happened or is in the process
            return true;
        }
        
        try {
            $service->removeDocumentById($document->uuid, KlinkVisibilityType::KLINK_PUBLIC);

            $this->publication->unpublished = true;

            $this->publication->save();

            return true;
        } catch (\Exception $ex) {
            \Log::error('Exception during document unpublishing', ['publication' => $this->publication, 'document' => $document, 'error' => $ex]);

            $this->publication->failed = true;
            
            $this->publication->save();

            return false;
        }
    }
}
