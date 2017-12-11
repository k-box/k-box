<?php

namespace KBox\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use KBox\File;

use Content\Services\ThumbnailsService;

use Exception;

/**
 * Job for generating the thumbnail of a {@see KBox\File}.
 *
 * It runs on the queue.
 *
 * @uses \Content\Services\ThumbnailsService
 */
class ThumbnailGenerationJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    /**
     *
     * @var KBox\File
     */
    private $file;
    
    /**
     *
     * @var boolean
     */
    private $force;

    /**
     * Create a new job instance.
     *
     * @param File $file the file you want to generate the thumbnail
     * @param boolean $force if the thumbnail generation should be forced (useful when the file already have a thumbnail)
     * @return ThumbnailGenerationJob
     */
    public function __construct(File $file, $force = false)
    {
        $this->file = $file;
        $this->force = $force;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ThumbnailsService $service)
    {
        try {
            $t_path = $service->generate($this->file, $this->force);
        } catch (Exception $ex) {
            \Log::error('Thumbnail generation Job error', [
                'file' => $this->file->toArray(),
                'force' => $this->force,
                'error' => $ex]);
        }
    }
}
