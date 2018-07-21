<?php

use Laracasts\TestDummy\Factory;
use KBox\Capability;
use KBox\DocumentDescriptor;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Console\Commands\DocumentsCheckInstitutionCommand;

use KBox\Traits\RunCommand;

/*
 * Test the DocumentsCheckInstitutionCommand
*/
class DocumentsCheckInstitutionCommandTest extends BrowserKitTestCase
{
    use DatabaseTransactions, RunCommand;
    
    public function testDocumentInstitutionCheckDifference()
    {
        $values = $this->createDocuments();
        
        $command = new DocumentsCheckInstitutionCommand(
            app('Klink\DmsAdapter\KlinkAdapter'),
            app('Klink\DmsDocuments\DocumentsService')
        );
        
        $res = $this->runArtisanCommand($command, []);
        
        $this->assertRegExp('/([\|\s]*)user: '.$values['user_institution'].', document: '.$values['doc_institution'].'([\s\w\S\W.]*)/', $res);
    }
    
    public function testDocumentInstitutionCheckFix()
    {
        $values = $this->createDocuments();
        
        $command = new DocumentsCheckInstitutionCommand(
            app('Klink\DmsAdapter\KlinkAdapter'),
            app('Klink\DmsDocuments\DocumentsService')
        );
        
        $res = $this->runArtisanCommand($command, [
            '--override-with-uploader' => true
            ]);
        
        $this->assertNotEmpty($res);
        
        $doc = DocumentDescriptor::findOrFail($values['doc_id']);
        
        $this->assertEquals($doc->institution_id, $values['user_institution']);
    }
    
    public function testDocumentInstitutionCheckFixAndIndexing()
    {
        $this->withKlinkAdapterFake();
        
        $values = $this->createDocuments(true);
        
        $command = new DocumentsCheckInstitutionCommand(
            app('Klink\DmsAdapter\KlinkAdapter'),
            app('Klink\DmsDocuments\DocumentsService')
        );
        
        $res = $this->runArtisanCommand($command, [
            '--override-with-uploader' => true,
            '--update-search-engine' => true
            ]);
        
        // $res = $output->fetch();
        
        $this->assertNotEmpty($res);
        
        $doc = DocumentDescriptor::findOrFail($values['doc_id']);
        
        $this->assertEquals($doc->institution_id, $values['user_institution']);
    }
    
    private function createDocuments($index_document = false)
    {
        $institution = factory('KBox\Institution')->create();
        $institution2 = factory('KBox\Institution')->create();
        
        $user = $this->createUser(Capability::$PROJECT_MANAGER, [
            'institution_id' => $institution->id
        ]);
        
        $file = factory('KBox\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KBox\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => false,
            'institution_id' => $institution2->id
        ]);
        
        if ($index_document) {
            app('Klink\DmsDocuments\DocumentsService')->reindexDocument($doc, 'private');
        }
        
        return ['doc_id' => $doc->id, 'doc_institution' => $doc->institution_id, 'user_institution' => $institution->id];
    }
}
