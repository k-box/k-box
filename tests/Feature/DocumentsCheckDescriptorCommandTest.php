<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\DocumentDescriptor;

/*
 * Test the DocumentsCheckDescriptorCommand
*/
class DocumentsCheckDescriptorCommandTest extends TestCase
{
    
    /**
     * Test the check descriptor command with a set of wrongly saved documents
     */
    public function testCheckDescriptorOnNonUpdatedDocuments()
    {
        $doc = DocumentDescriptor::factory()->create([
            'is_public' => false,
            'language' => 'en',
            'document_type' => 'image',
            'mime_type' => 'image/png',
        ]);

        $file = $doc->file;

        $new_mime_type = $file->mime_type;
        $new_document_type = $file->document_type;
        $new_hash = $file->hash;

        $this->artisan('documents:check-latest-version')
            ->assertExitCode(0);

        $updated_descriptor = $doc->fresh();

        $this->assertEquals($updated_descriptor->mime_type, $new_mime_type, 'Mime type not matching');
        $this->assertEquals($updated_descriptor->document_type, $new_document_type, 'Document type not matching');
        $this->assertEquals($updated_descriptor->hash, $new_hash, 'Hash not matching');
        $this->assertEquals($updated_descriptor->local_document_id, $doc->local_document_id, 'Local document ID not matching');
    }

    public function testCheckDescriptorOnNonExistingDocument()
    {
        $doc = DocumentDescriptor::factory()->create();

        $doc->forceDelete();

        $this->artisan('documents:check-latest-version', [
                'document' => $doc->id
            ])
            ->expectsOutput("Document not found")
            ->assertExitCode(127);
    }
}
