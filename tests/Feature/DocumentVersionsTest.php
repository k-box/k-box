<?php

namespace Tests\Feature;

use KBox\File;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentVersionsTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    public function test_version_show_redirects_to_klink_route()
    {
        Storage::fake('local');

        $document = factory('KBox\DocumentDescriptor')->create();

        $response = $this->get("/documents/$document->id/versions/100");

        $response->assertRedirect("/klink/$document->local_document_id/preview/100");
    }

    public function test_delete_last_file_revision()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory('KBox\DocumentDescriptor')->create();
        
        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $response = $this->delete("/documents/$document->id/versions/$last_version->uuid");

        $response->assertRedirect("/documents/$document->id/edit");

        $document_after_version_removal = $document->fresh();

        $this->assertEquals($first_version->id, $document_after_version_removal->file_id, "Not the expected file version");
        $this->assertEquals('text/html', $document_after_version_removal->mime_type);
        $this->assertEquals($first_version->hash, $document_after_version_removal->hash);
        $this->assertNull(File::withTrashed()->find($last_version->id));

        $adapter->assertDocumentIndexed($document->uuid);
    }

    public function test_delete_oldest_file_revision()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory('KBox\DocumentDescriptor')->create();
        
        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $response = $this->delete("/documents/$document->id/versions/$first_version->uuid");

        $response->assertRedirect("/documents/$document->id/edit");

        $document_after_version_removal = $document->fresh();

        $this->assertEquals($last_version->id, $document_after_version_removal->file_id, "Not the expected file version");
        $this->assertEquals($last_version->mime_type, $document_after_version_removal->mime_type);
        $this->assertEquals($last_version->hash, $document_after_version_removal->hash);
        $this->assertNull($last_version->fresh()->revision_of);
        $this->assertNull(File::withTrashed()->find($first_version->id));
    }
    
    public function test_delete_middle_file_revision()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory('KBox\DocumentDescriptor')->create();
        
        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);
        
        $middle_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $middle_version->id;
        $last_version->save();
        
        $middle_version->revision_of = $first_version->id;
        $middle_version->save();

        $response = $this->delete("/documents/$document->id/versions/$middle_version->uuid");

        $response->assertRedirect("/documents/$document->id/edit");

        $document_after_version_removal = $document->fresh();

        $this->assertEquals($last_version->id, $document_after_version_removal->file_id, "Not the expected file version");
        $this->assertEquals($last_version->mime_type, $document_after_version_removal->mime_type);
        $this->assertEquals($first_version->id, $last_version->fresh()->revision_of);
        $this->assertNull(File::withTrashed()->find($middle_version->id));
    }

    public function test_file_versions_are_returned()
    {
        Storage::fake('local');
        
        $old_revision = factory('KBox\File')->create();
        
        $mid_revision = factory('KBox\File')->create([
            'revision_of' => $old_revision->id,
        ]);
        
        $new_revision = factory('KBox\File')->create([
            'revision_of' => $mid_revision->id,
        ]);

        $versions = $new_revision->versions();

        $this->assertEquals(2, $versions->count());
        $this->assertEquals([$mid_revision->uuid, $old_revision->uuid], $versions->pluck('uuid')->toArray());
    }

    public function test_restore_middle_file_revision()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory('KBox\DocumentDescriptor')->create();
        
        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);
        
        $middle_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $middle_version->id;
        $last_version->save();
        
        $middle_version->revision_of = $first_version->id;
        $middle_version->save();

        $response = $this->put("/documents/$document->id/versions/$middle_version->uuid/restore");

        $response->assertRedirect("/documents/$document->id/edit");

        $document_after_version_removal = $document->fresh();

        $this->assertEquals($middle_version->id, $document_after_version_removal->file_id, "Not the expected file version");
        $this->assertEquals($middle_version->mime_type, $document_after_version_removal->mime_type);
        $this->assertNull(File::withTrashed()->find($last_version->id));
        $adapter->assertDocumentIndexed($document->uuid);
    }

    public function test_restore_first_file_revision()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory('KBox\DocumentDescriptor')->create();
        
        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);
        
        $middle_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $middle_version->id;
        $last_version->save();
        
        $middle_version->revision_of = $first_version->id;
        $middle_version->save();

        $response = $this->put("/documents/$document->id/versions/$first_version->uuid/restore");

        $response->assertRedirect("/documents/$document->id/edit");

        $document_after_version_removal = $document->fresh();

        $this->assertEquals($first_version->id, $document_after_version_removal->file_id, "Not the expected file version");
        $this->assertEquals($first_version->mime_type, $document_after_version_removal->mime_type);
        $this->assertNull($first_version->fresh()->revision_of);
        $this->assertNull(File::withTrashed()->find($last_version->id));
        $this->assertNull(File::withTrashed()->find($middle_version->id));
        $adapter->assertDocumentIndexed($document->uuid);
    }
}
