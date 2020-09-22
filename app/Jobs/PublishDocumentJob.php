<?php

namespace KBox\Jobs;

use Illuminate\Bus\Queueable;
use KBox\Publication;
use KBox\Option;
use KBox\Facades\KlinkStreaming;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use KBox\Documents\Services\DocumentsService;
use Klink\DmsAdapter\KlinkVisibilityType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \KBox\Publication
     */
    public $publication;

    /**
     * @var bool
     */
    public $force;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Publication $publication, $force = false)
    {
        $this->publication = $publication;
        $this->force = $force;
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

        if ($this->publication->published && ! $this->force) {
            // Abort as publication is already happened
            return true;
        }

        try {
            if ($document->file->isVideo() && network_enabled() && ! empty(Option::option(Option::STREAMING_SERVICE_URL, null))) {
                try {
                    $stream = $this->pushVideoToStreamingService($document->file);
    
                    $this->publication->streaming_url = $stream->url;
                    $this->publication->streaming_id = $stream->video_id;
    
                    $this->publication->save();
                } catch (\Exception $ex) {
                    \Log::error('Video sending to streaming service error', ['publication' => $this->publication, 'document' => $document, 'error' => $ex]);
                    throw $ex;
                }
            }

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

    private function pushVideoToStreamingService($file)
    {
        \Log::info("Upload to straming service started for file $file->id");

        $upload = KlinkStreaming::upload($file->absolute_path);

        \Log::info("Upload to streaming service completed for file $file->id");

        $video_id = $upload->videoId();

        $streaming = KlinkStreaming::get($video_id);

        return $streaming;
    }
}
