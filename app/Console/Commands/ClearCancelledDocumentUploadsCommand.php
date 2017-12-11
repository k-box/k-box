<?php

namespace KBox\Console\Commands;

use Log;
use Illuminate\Console\Command;
use KBox\DocumentDescriptor;
use Avvertix\TusUpload\TusUpload;

class ClearCancelledDocumentUploadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:clear-cancelled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the cancelled document uploads from the system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DocumentDescriptor::whereStatus(DocumentDescriptor::STATUS_UPLOAD_CANCELLED)->chunk(100, function ($cancelled) {
            Log::info("{$cancelled->count()}");
            
            $cancelled->each(function ($document) {
                $upload_request_id = $document->file->request_id;

                if ($upload_request_id !== null) {
                    TusUpload::where('request_id', $upload_request_id)->delete();
                }

                $document->forceDelete();
            });
        });
        
        Log::info('Cleared cancelled uploads');
    }
}
