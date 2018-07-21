<?php

use Laracasts\TestDummy\Factory;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Console\Commands\DocumentsCheckDescriptorCommand;
use Klink\DmsAdapter\KlinkDocumentUtils;
use KBox\Traits\RunCommand;

/*
 * Test the DocumentsCheckDescriptorCommand
*/
class DocumentsCheckDescriptorCommandTest extends BrowserKitTestCase
{
    use DatabaseTransactions, RunCommand;

    // function that might be useful

    private function createWrongDocument($options = [])
    {
        $docs = factory('KBox\DocumentDescriptor')->create($options);
        
        return $docs;
    }

    // real test methods

    /**
     * Test the check descriptor command with a set of wrongly saved documents
     */
    public function testCheckDescriptorOnNonUpdatedDocuments()
    {
        $doc = $this->createWrongDocument([
            'is_public' => false,
            'language' => 'en',
            'document_type' => 'image',
            'mime_type' => 'image/png',
        ]);

        $file = $doc->file;

        $new_mime_type = $file->mime_type;
        $new_document_type = KlinkDocumentUtils::documentTypeFromMimeType($file->mime_type);
        $new_hash = $file->hash;

        $command = new DocumentsCheckDescriptorCommand(app('Klink\DmsDocuments\DocumentsService'));

        $res = $this->runArtisanCommand($command, []);

        $updated_descriptor = $doc->fresh();

        $this->assertEquals($updated_descriptor->mime_type, $new_mime_type, 'Mime type not matching');
        $this->assertEquals($updated_descriptor->document_type, $new_document_type, 'Document type not matching');
        $this->assertEquals($updated_descriptor->hash, $new_hash, 'Hash not matching');
        $this->assertEquals($updated_descriptor->local_document_id, $doc->local_document_id, 'Local document ID not matching');
    }

    /**
     * Test the check descriptor command with a non existing document
     * @expectedException     Illuminate\Database\Eloquent\ModelNotFoundException
     * @expectedExceptionMessage No query results for model [KBox\DocumentDescriptor]
     */
    public function testCheckDescriptorOnNonExistingDocument()
    {
        $doc = $this->createWrongDocument();

        $doc->forceDelete();

        $command = new DocumentsCheckDescriptorCommand(app('Klink\DmsDocuments\DocumentsService'));
        
        $res = $this->runArtisanCommand($command, [
            'document' => $doc->id
        ]);
    }
}
