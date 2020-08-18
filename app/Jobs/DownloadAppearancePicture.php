<?php

namespace KBox\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use KBox\Appearance\HeroPicture;

class DownloadAppearancePicture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $picture;
    
    public $force = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($picture, $force = false)
    {
        $this->picture = $picture;
        $this->force = $force;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new HeroPicture($this->picture))->fetch($this->force);
    }
}
