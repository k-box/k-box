<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Institution;
use Klink\DmsAdapter\KlinkAdapter;
use Klink\DmsDocuments\DocumentsService;

use KlinkDMS\Console\Traits\DebugOutput;

use Exception;
use KlinkException;
use Log;
use DB;

class DocumentsCheckDescriptorCommand extends Command
{
    
    use DebugOutput;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:check-latest-version {document? : The ID of documents to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the latest version details of a Document are correctly reported in a document descriptor';



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( DocumentsService $service )
    {
        parent::__construct();
        
        $this->doc_service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $document = $this->argument('document');
        
        
        $query = DocumentDescriptor::local()->private(); 
		
		$docs = collect();

        if(!is_null($document)){
            $d = DocumentDescriptor::findOrFail( $document );

            $docs = collect( [ $d ] );
        }
        else {
            $docs = $query->get();
        }

        $last_modified_on = null;

		$reindexed_documents_count = 0;

        $count_docs = $docs->count();

        $this->line("Checking and fixing <info>". $count_docs ." documents</info>...");

        $file = null;

        $to_reindex = [];
		
		for($i = 0; $i < $count_docs; $i++){
		
			$doc = $docs[$i];

            $file = $doc->file;

            
            $doc->mime_type = $file->mime_type;
            $doc->document_type = \KlinkDocumentUtils::documentTypeFromMimeType( $file->mime_type );
            $doc->hash = $file->hash;

			$doc->timestamps = false; //temporarly disable the automatic upgrade of the updated_at field

            if($doc->isDirty()){
                $doc->save();

                array_push($to_reindex, $doc->id);
            }
			

        }

        if(count($to_reindex) > 0){
            $this->line("The following documents must be reindexed: <info>". implode(' ', $to_reindex) ."</info>");
        }
        else {
            $this->line("<info>No documents with problems found.</info>");
        }

        
        
        return 0;
    }
    
    
}
