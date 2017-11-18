<?php

namespace KlinkDMS\Jobs;

use Illuminate\Bus\Queueable;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Publication;
use KlinkDMS\Option;
use KlinkDMS\Facades\KlinkStreaming;
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
            if ($this->document->file->isVideo() &&
                network_enabled() &&
                ! empty(Option::option(Option::STREAMING_SERVICE_URL, null)) &&
                $this->document->file->updated_at->gt($publication->published_at)) {
                try {
                    KlinkStreaming::delete($publication->streaming_id);

                    $stream = $this->pushVideoToStreamingService($this->document->file);

                    $publication->streaming_url = $stream->url;
                    $publication->streaming_id = $stream->video_id;

                    $publication->save();
                } catch (\Exception $ex) {
                    \Log::error('Updating streaming video error', ['publication' => $publication, 'document' => $this->document, 'error' => $ex]);
                    throw $ex;
                }
            }

            $returned_descriptor = $service->updateDocumentProxy($this->document, $this->document->file, KlinkVisibilityType::KLINK_PUBLIC);

            $publication->touch();
            $publication->save();

            return true;
        } catch (\Exception $ex) {
            \Log::error('Exception during update existing document publication', ['publication' => $publication, 'document' => $this->document, 'error' => $ex]);

            $publication->failed = true;
            
            $publication->save();

            return false;
        }
    }

    private function pushVideoToStreamingService($file)
    {
        \Log::info("Upload to streaming service started for file $file->id");

        $upload = KlinkStreaming::upload($file->absolute_path);

        \Log::info("Upload to streaming service completed for file $file->id");

        $video_id = $upload->videoId();

        $streaming = KlinkStreaming::get($video_id);

        return $streaming;
    }
}
