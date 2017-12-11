<?php

namespace KBox\Jobs;

use Log;
use Exception;
use Illuminate\Bus\Queueable;
use KBox\DocumentDescriptor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use KBox\DocumentsElaboration\Kernel as ElaborationKernel;

class ElaborateDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \KBox\DocumentDescriptor
     */
    public $descriptor = null;

    /**
     * Create a new job instance.
     *
     * @param \KBox\DocumentDescriptor $descriptor
     * @return void
     */
    public function __construct($descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("Starting Document Elaboration Pipeline for {$this->descriptor->uuid}");
            
            app(ElaborationKernel::class)
            ->handle($this->descriptor);
        } catch (Exception $ex) {
            Log::info("Elaboration Pipeline unhandled exception for {$this->descriptor->uuid}", ['descriptor' => $this->descriptor, 'error' => $ex]);

            $this->descriptor->status = DocumentDescriptor::STATUS_ERROR;
            $this->descriptor->last_error = $ex;
            $this->descriptor->save();
        }
    }
}
