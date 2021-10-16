<?php

namespace Tests\Feature;

use KBox\File;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use KBox\DocumentDescriptor;
use KBox\Events\DocumentVersionUploaded;
use KBox\Events\UploadCompleted;
use KBox\Facades\Quota;
use KBox\User;
use KBox\Project;

class DocumentVersionsTest extends TestCase
{
    use WithoutMiddleware;

    public function test_version_show_redirects_to_document_preview_route()
    {
        Storage::fake('local');

        $document = DocumentDescriptor::factory()->create();

        $response = $this->get("/documents/$document->id/versions/100");

        $response->assertRedirect("/d/show/$document->uuid/100");
    }

    public function test_delete_last_file_revision()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = DocumentDescriptor::factory()->create();
        
        $last_version = $document->file;

        $first_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $response = $this->actingAs($document->owner)->delete("/documents/$document->id/versions/$last_version->uuid");

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
        
        $document = DocumentDescriptor::factory()->create();
        
        $last_version = $document->file;

        $first_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $response = $this->actingAs($document->owner)->delete("/documents/$document->id/versions/$first_version->uuid");

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
        
        $document = DocumentDescriptor::factory()->create();
        
        $last_version = $document->file;

        $first_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);
        
        $middle_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $middle_version->id;
        $last_version->save();
        
        $middle_version->revision_of = $first_version->id;
        $middle_version->save();

        $response = $this->actingAs($document->owner)->delete("/documents/$document->id/versions/$middle_version->uuid");

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
        
        $old_revision = File::factory()->create();
        
        $mid_revision = File::factory()->create([
            'revision_of' => $old_revision->id,
        ]);
        
        $new_revision = File::factory()->create([
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
        
        $document = DocumentDescriptor::factory()->create();
        
        $last_version = $document->file;

        $first_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);
        
        $middle_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $middle_version->id;
        $last_version->save();
        
        $middle_version->revision_of = $first_version->id;
        $middle_version->save();

        $response = $this->actingAs($document->owner)->put("/documents/$document->id/versions/$middle_version->uuid/restore");

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
        
        $document = DocumentDescriptor::factory()->create();
        
        $last_version = $document->file;

        $first_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);
        
        $middle_version = File::factory()->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $middle_version->id;
        $last_version->save();
        
        $middle_version->revision_of = $first_version->id;
        $middle_version->save();

        $response = $this->actingAs($document->owner)->put("/documents/$document->id/versions/$first_version->uuid/restore");

        $response->assertRedirect("/documents/$document->id/edit");

        $document_after_version_removal = $document->fresh();

        $this->assertEquals($first_version->id, $document_after_version_removal->file_id, "Not the expected file version");
        $this->assertEquals($first_version->mime_type, $document_after_version_removal->mime_type);
        $this->assertNull($first_version->fresh()->revision_of);
        $this->assertNull(File::withTrashed()->find($last_version->id));
        $this->assertNull(File::withTrashed()->find($middle_version->id));
        $adapter->assertDocumentIndexed($document->uuid);
    }

    public function test_add_new_version_blocked_because_of_quota()
    {
        Storage::fake('local');
        $this->withKlinkAdapterFake();

        config([
            'quota.user' => 1024, // bytes
        ]);

        $user = User::factory()->admin()->create();

        $quota = Quota::user($user);
        
        $document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id
        ]);

        $response = $this->actingAs($user)->from(route('documents.edit', $document->id))->put(route('documents.update', $document->id), [
            'document' => UploadedFile::fake()->create('document.pdf', 100)
        ]);

        $response->assertRedirect("/documents/$document->id/edit");

        $message = trans('documents.update.error', ['error' => trans('quota.not_enough_free_space', ['necessary_free_space' => human_filesize(100*1024-1024), 'quota' => human_filesize($quota->limit)])]);
        
        $response->assertSessionHasErrors(['error' => $message]);
    }

    public function test_add_new_version_trigger_events()
    {
        Storage::fake('local');
        $this->withKlinkAdapterFake();

        $user = User::factory()->admin()->create();
        
        $document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id
        ]);

        Event::fake();

        $response = $this
            ->actingAs($user)
            ->from(route('documents.edit', $document->id))
            ->put(route('documents.update', $document->id), [
                'document' => UploadedFile::fake()->create('document.pdf', 100)
            ]);

        $response->assertRedirect("/documents/$document->id/edit");

        $response->assertSessionHas('flash_message', trans('documents.messages.updated'));

        $file = $document->fresh()->file;

        Event::assertDispatched(DocumentVersionUploaded::class, function ($e) use ($file, $user, $document) {
            return $e->file->is($file)
                && $e->descriptor->is($document)
                && $e->user->is($user);
        });
        
        Event::assertDispatched(UploadCompleted::class, function ($e) use ($document, $user) {
            return $e->descriptor->is($document) && $e->user->is($user);
        });
    }
}
