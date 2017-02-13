<?php

namespace KlinkDMS\Jobs;

use KlinkDMS\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use KlinkDMS\File;

use Klink\DmsPreviews\Thumbnails\ThumbnailsService;

use Exception;

/**
 * Job to generate a thumbnail for a File.
 */
class ThumbnailGenerationJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    /**
     *
     * @var KlinkDMS\File
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
        try
        {

            $t_path = $service->generate($this->file, $this->force);
        
        }
        catch(Exception $ex)
        {
            
            \Log::error('Thumbnail generation Job error', array(
                'file' => $this->file->toArray(), 
                'force' => $this->force, 
                'error' => $ex));
            
        }
    }
}
