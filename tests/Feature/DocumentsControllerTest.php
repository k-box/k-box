<?php

namespace Tests\Feature;

use Tests\TestCase;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentsControllerTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    /**
     * Integration test of file upload via form.
     * It tests also the event and listeners pipeline after a file is uploaded
     */
    public function test_file_upload_via_form()
    {
        Storage::fake('local');

        $adapter = $this->withKlinkAdapterFake();

        $user = factory('KlinkDMS\User')->create();

        $response = $this->actingAs($user)->json('POST', '/documents', [
            'document' => UploadedFile::fake()->create('document.pdf', 10)
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'descriptor' => [
                'title',
                'hash',
                'mime_type',
                'status',
            ]
        ]);

        // grabbing the descriptor instance from the response
        $descriptor = $response->original['descriptor'];

        $this->assertEquals('document.pdf', $descriptor->title);
        $this->assertNotNull($descriptor->file_id);
        $this->assertNotNull($descriptor->owner_id);
        $this->assertNotNull($descriptor->uuid);
        $this->assertEquals('application/pdf', $descriptor->mime_type);
        $this->assertEquals(DocumentDescriptor::STATUS_COMPLETED, $descriptor->status, 'Document status not COMPLETED');

        $adapter->assertDocumentIndexed($descriptor->uuid);

        $file = $descriptor->file;

        $folder = date('Y').'/'.date('m');
        Storage::disk('local')->assertExists("{$folder}/{$file->uuid}/");
        Storage::disk('local')->assertExists($file->path);
    }
}
