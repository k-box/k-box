<?php

namespace KlinkDMS\Jobs;

use KlinkDMS\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use KlinkDMS\File;

use Klink\DmsDocuments\DocumentsService;

use Exception;

/**
 * Job to generate a thumbnail for a File.
 */
class ThumbnailGenerationJob extends Job implements SelfHandling, ShouldQueue
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
     * @return void
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
    public function handle(DocumentsService $service)
    {
        try{
        
            $is_webpage = $this->file->isRemoteWebPage();
            
            $t_path = $service->generateThumbnail($this->file, 'default', $this->force, $is_webpage);
        
        }catch(Exception $ex){
            
            \Log::error('Thumbnail generation Job error', array('file' => $this->file->toArray(), 'force' => $this->force, 'error' => $ex));
            
        }
    }
}
