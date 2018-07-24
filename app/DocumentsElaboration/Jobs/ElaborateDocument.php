<?php

namespace KBox\DocumentsElaboration\Jobs;

use Log;
use Exception;
use Illuminate\Bus\Queueable;
use KBox\DocumentDescriptor;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use KBox\DocumentsElaboration\DocumentElaborationManager;

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
     * @param \KBox\DocumentsElaboration\DocumentElaborationManager $manager
     * @return void
     */
    public function handle(DocumentElaborationManager $manager)
    {
        try {
            Log::info("Starting Document Elaboration Pipeline for {$this->descriptor->uuid}");
            
            app(Pipeline::class)
                ->send($this->descriptor)
                ->through($manager->actions())
                ->then(function ($descriptor) {
                    if ($descriptor->status !== DocumentDescriptor::STATUS_ERROR) {
                        $descriptor->status = DocumentDescriptor::STATUS_COMPLETED;
        
                        $descriptor->save();
                    }

                    return $descriptor->fresh();
                });
        } catch (Exception $ex) {
            Log::info("Elaboration Pipeline unhandled exception for {$this->descriptor->uuid}", ['descriptor' => $this->descriptor, 'error' => $ex]);

            $this->descriptor->status = DocumentDescriptor::STATUS_ERROR;
            $this->descriptor->last_error = $ex;
            $this->descriptor->save();
        }
    }
}
