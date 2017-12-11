<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use KBox\DocumentDescriptor;
use KBox\Jobs\ThumbnailGenerationJob;

use Exception;
use Log;

class ThumbnailGenerationCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbnail:generate {documents?* : (optional) The ID (or the IDs) of the document(s) that need a thumbnail}{--force : Set this to force the recreation of an existing thumbnail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the thumbnail of Document Descriptors';

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
        $args = $this->argument('documents');
        $force = $this->option('force');
        
        $this->comment('Generating thumbnails...');
        
        $documents = empty($args) || ! is_array($args) ? DocumentDescriptor::all() : DocumentDescriptor::whereIn('id', $args)->get();
        
        $total = $documents->count();
        
        if (! empty($args) && count($args) > $total) {
            $diff = array_diff($args, $documents->pluck('id')->toArray());
            throw new ModelNotFoundException('One or more documents cannot be found: '.implode(', ', $diff));
        }
        
        $bar = $this->output->createProgressBar($total);
        
        $this->comment('  '.$total.' document(s)');
        
        $bar->start();
        
        $errors = [];
        
        foreach ($documents as $document) {
            try {
                dispatch(new ThumbnailGenerationJob($document->file, $force));
            } catch (Exception $ex) {
                Log::error('Console Thumbnail generation error for DocumentDescriptor '.$document->id, ['error' => $ex]);
                
                $errors[] = ['document' => $document->id, 'error' => $ex->getMessage()];
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        if (! empty($errors)) {
            $headers = ['Document', 'Error'];
            
            $this->error('The thumbnail generation for '.count($errors).' raised error.');
            
            $this->table($headers, $errors);
        }
        
        return 0;
    }
}
