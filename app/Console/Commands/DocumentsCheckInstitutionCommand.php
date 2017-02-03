<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Institution;
use Klink\DmsAdapter\Contracts\KlinkAdapter;
use Klink\DmsDocuments\DocumentsService;

use KlinkDMS\Console\Traits\DebugOutput;

use Exception;
use KlinkException;
use Log;
use DB;

class DocumentsCheckInstitutionCommand extends Command
{
    
    use DebugOutput;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:check-affiliation {--override-with-uploader : Set this to pick the institution from the current uploader of the document}{--update-search-engine : Reflect the institution change also in the search engine if needed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if all the documents has the same institution of the first uploader. The command assumes that the user affiliation has not been changed since the upload of the document';


    private $adapter = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( KlinkAdapter $adapter, DocumentsService $service )
    {
        parent::__construct();
        
        $this->adapter = $adapter;
        $this->doc_service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $force = $this->option('override-with-uploader');
        $force_search_engine_update = $this->option('update-search-engine');
        
        $this->comment('Checking Document institutions...');
        
        $documents = DocumentDescriptor::with('owner')->get()->filter(function($d){
            
            return !is_null($d->owner) && $d->institution_id != $d->owner->institution_id;
        });
        
        // dd($documents->toArray());
        
        $total = $documents->count();
        
        
        $bar = $this->output->createProgressBar( $total );
        
        $this->comment('  ' . $total . ' document(s)');
        
        $bar->start();
        
        $errors = [];
        
        $old_institution_id = null;
        $owner_inst = null;
        $transaction_started = false;
        foreach ($documents as $document) {
            
            try{
            
                if($force && !is_null($document->owner)){
                    
                    DB::beginTransaction();
                    $transaction_started = true;
                    
                    $old_institution_id = $document->institution_id; 
                    
                    $document->institution_id = $document->owner->institution_id;
                    
                    $document->save();
                    
                    if($force_search_engine_update){
                        
                        // check if the search engine knows the document also with the old institution
                        
                        $old_doc = $this->getDocumentFromCore($document, $old_institution_id);
                        $new_doc = $this->getDocumentFromCore($document, $document->owner->institution_id);
                        
                        if(is_null($new_doc) && !is_null($old_doc)){
                            // indexed using old institution, update needed
                            $this->debugLine('Reindexing document '. $document->id .' with new institution');
                            
                            $this->doc_service->reindexDocument($document, 'private');
                            
                            if($document->is_public){
                                $this->doc_service->reindexDocument($document, 'public');
                            }
                            
                            $owner_inst = $this->getKlinkIdForInstitution($old_institution_id);
                            
                            $this->adapter->removeDocumentById( $owner_inst, $document->local_document_id, 'private');
                        }
                        
                        if(!is_null($new_doc) && !is_null($old_doc)){
                            // indexed with both, remove the old one
                            
                            $owner_inst = $this->getKlinkIdForInstitution($old_institution_id);
                            
                            $this->debugLine('Removing document '. $document->id .' ('. $owner_inst .'-'. $document->local_document_id .') with old institution');
                            
                            $this->adapter->removeDocumentById( $owner_inst, $document->local_document_id, 'private');
                            
                        }
                    }
                    
                    // end transaction
                    DB::commit();
                    $transaction_started = false; 
                }
                else {
                    
                    $owner_inst = is_null($document->owner) ? 'null' : $document->owner->institution_id;
                    
                    $errors[] = ['document' => $document->id, 'error' => 'Different Institution (user: '.$owner_inst.', document: '. $document->institution_id .')'];
                }
                
            
            }catch(Exception $ex){
                
                if($transaction_started){
                    DB::rollBack();
                }
                
                Log::error('Console Document institution update ' . $document->id, ['error' => $ex]);
                
                $errors[] = ['document' => $document->id, 'error' => $ex->getMessage()];
                
            }
            
            $bar->advance();
            
        }
        
        $bar->finish();
        
        if(!empty($errors)){
            $headers = ['Document', 'Error'];
            
            $this->line(' ');
            
            $this->table($headers, $errors);
        }
        
        return 0;
    }
    
    
    protected function getKlinkIdForInstitution($id){
        return Institution::findOrFail($id)->klink_id;
    }
    
    protected function getDocumentFromCore(DocumentDescriptor $document, $institution_id){
        
        try{
            
            $inst = $this->getKlinkIdForInstitution($institution_id);
            
            return $this->adapter->getDocument( $inst, $document->local_document_id, 'private');
            
        }catch(KlinkException $kex){
            
            return null;
            
        }
    }
}
