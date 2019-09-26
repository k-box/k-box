<?php

namespace Tests\Feature;

use KBox\File;
use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\Institution;
use KBox\DocumentDescriptor;
use KBox\Documents\Services\DocumentsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Test the DocumentsCheckInstitutionCommand
*/
class DocumentsCheckInstitutionCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();
        DB::table('document_descriptors')->truncate();
        DB::table('files')->truncate();
    }
    
    public function testDocumentInstitutionCheckDifference()
    {
        $values = $this->createDocuments();

        $expected_doc_id = str_pad($values['doc_id'], 9);

        $this->artisan('documents:check-affiliation')
            ->expectsOutput('Checking Document institutions...')
            ->expectsOutput('  1 document(s)')
            ->expectsOutput("| {$expected_doc_id}| Different Institution (user: {$values['user_institution']}, document: {$values['doc_institution']}) |")
            ->assertExitCode(0);
    }
    
    public function testDocumentInstitutionCheckFix()
    {
        $values = $this->createDocuments();

        $this->artisan('documents:check-affiliation', [
            '--override-with-uploader' => true
            ])
            ->expectsOutput('Checking Document institutions...')
            ->expectsOutput('  1 document(s)')
            ->assertExitCode(0);
        
        $doc = DocumentDescriptor::findOrFail($values['doc_id']);
        
        $this->assertEquals($doc->institution_id, $values['user_institution']);
    }
    
    public function testDocumentInstitutionCheckFixAndIndexing()
    {
        $adapter = $this->withKlinkAdapterFake();
        
        $values = $this->createDocuments(true);
            
        $this->artisan('documents:check-affiliation', [
                '--override-with-uploader' => true,
                '--update-search-engine' => true
            ])
            ->expectsOutput('Checking Document institutions...')
            ->expectsOutput('  1 document(s)')
            ->assertExitCode(0);
        
        $doc = DocumentDescriptor::findOrFail($values['doc_id']);
        
        $this->assertEquals($doc->institution_id, $values['user_institution']);

        $adapter->assertDocumentIndexed($doc->uuid);
    }
    
    private function createDocuments($index_document = false)
    {
        $institution = factory(Institution::class)->create();
        $institution2 = factory(Institution::class)->create();
        
        $user = tap(factory(User::class)->create([
            'institution_id' => $institution->id
        ]), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $file = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => false,
            'institution_id' => $institution2->id
        ]);
        
        if ($index_document) {
            app(DocumentsService::class)->reindexDocument($doc, 'private');
        }
        
        return ['doc_id' => $doc->id, 'doc_institution' => $doc->institution_id, 'user_institution' => $institution->id];
    }
}
