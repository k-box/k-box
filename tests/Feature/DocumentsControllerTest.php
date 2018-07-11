<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Capability;
use KBox\DuplicateDocument;
use KBox\DocumentDescriptor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentsControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Integration test of file upload via form.
     * It tests also the event and listeners pipeline after a file is uploaded
     */
    public function test_file_upload_via_form()
    {
        $this->withoutMiddleware();

        Storage::fake('local');

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

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

    public function test_duplicate_badge_is_shown_when_listing_documents_that_have_duplicates()
    {
        Storage::fake('local');

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicates = $this->createDuplicates($user, 1, ['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.documents');
        $response->assertSee(trans('documents.duplicates.badge'));
    }

    public function test_duplicate_badge_is_not_shown_if_another_user_logs_in()
    {
        Storage::fake('local');

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicates = $this->createDuplicates($user);

        $response = $this->actingAs($user)->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.documents');
        $response->assertDontSee(trans('documents.duplicates.badge'));
    }

    
    public function test_duplicate_actions_are_presented_on_document_edit_page()
    {
        $this->disableExceptionHandling();

        Storage::fake('local');

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();

        $response = $this->actingAs($user)->get(route('documents.edit', ['id' => $duplicate->document->id]));

        $response->assertStatus(200);
        $response->assertViewIs('documents.edit');
        $response->assertSee(trans('documents.duplicates.duplicates_btn'));
        $response->assertSee(trans('documents.duplicates.duplicates_description'));
    }

    private function createDuplicates($user, $count = 1, $options = [])
    {
        return factory(DuplicateDocument::class, $count)->create($options);
    }
}
