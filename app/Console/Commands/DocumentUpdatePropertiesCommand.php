<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KlinkDMS\DocumentsElaboration\Actions\ExtractFileProperties;
use KlinkDMS\DocumentDescriptor;

use Exception;
use Log;

class DocumentUpdatePropertiesCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:properties-update {documents?* : (optional) The ID (or the IDs) of the document(s)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the properties of the document latest version';

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
        // $force = $this->option('force');
        
        $this->comment('Updating document properties for video files...');
        
        $documents = empty($args) || ! is_array($args) ? DocumentDescriptor::where('mime_type', 'video/mp4')->get() : DocumentDescriptor::whereIn('id', $args)->get();
        
        $total = $documents->count();
        
        if (! empty($args) && count($args) > $total) {
            $diff = array_diff($args, $documents->pluck('id')->toArray());
            throw new ModelNotFoundException('One or more documents cannot be found: '.implode(', ', $diff));
        }
        
        $bar = $this->output->createProgressBar($total);
        
        $this->comment('  '.$total.' document(s)');
        
        $bar->start();
        
        $errors = [];
        
        $action = new ExtractFileProperties();
        
        foreach ($documents as $document) {
            try {
                $action->run($document);
            } catch (Exception $ex) {
                Log::error('Document update properties error for '.$document->id, ['error' => $ex]);
                
                $errors[] = ['document' => $document->id, 'error' => $ex->getMessage()];
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        if (! empty($errors)) {
            $headers = ['Document', 'Error'];
            
            $this->error('Update properties was not possible for '.count($errors).' documents.');
            
            $this->table($headers, $errors);
        }
        
        return 0;
    }
}
