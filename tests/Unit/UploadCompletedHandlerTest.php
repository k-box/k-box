<?php

namespace Tests\Unit;

use KBox\File;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Events\UploadCompleted;
use Tests\Concerns\ClearDatabase;
use Illuminate\Support\Facades\Event;
use KBox\Events\FileDuplicateFoundEvent;
use KBox\Listeners\UploadCompletedHandler;

use Illuminate\Support\Facades\Bus;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;
use KBox\Jobs\CalculateUserUsedQuota;

class UploadCompletedHandlerTest extends TestCase
{
    use ClearDatabase;

    public function test_elaborate_document_is_dispatched()
    {
        DocumentElaboration::fake();
        $descriptor = DocumentDescriptor::factory()->create();
        $user = $descriptor->owner;

        $uploadCompleteEvent = new UploadCompleted($descriptor, $user);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        DocumentElaboration::assertQueued($descriptor);
    }

    public function test_descriptor_status_is_updated()
    {
        DocumentElaboration::fake();
        $descriptor = DocumentDescriptor::factory()->create([
            'status' =>  DocumentDescriptor::STATUS_UPLOADING
        ]);
        $user = $descriptor->owner;
        $file = $descriptor->file;
        $file->upload_completed_at = null;
        $file->save();

        $uploadCompleteEvent = new UploadCompleted($descriptor, $user);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        $descriptor = $descriptor->fresh();
        $file = $file->fresh();
        
        $this->assertNotNull($file->upload_completed_at);
        $this->assertEquals(DocumentDescriptor::STATUS_PROCESSING, $descriptor->status);
    }

    public function test_duplicate_found_is_raised_when_the_user_upload_the_same_document_twice()
    {
        $descriptor = DocumentDescriptor::factory()->create();
        $user = $descriptor->owner;

        $duplicate = DocumentDescriptor::factory()->create([
            'hash' => $descriptor->hash,
            'owner_id' => $user->id
        ]);
        $file = $descriptor->file;

        $uploadCompleteEvent = new UploadCompleted($descriptor, $user);

        Event::fake([FileDuplicateFoundEvent::class]);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        Event::assertDispatched(FileDuplicateFoundEvent::class, function ($e) use ($user, $descriptor, $duplicate) {
            return $e->user->is($user) &&
                $e->duplicateDocument->duplicate_document_id === $descriptor->id &&
                $e->duplicateDocument->document_id === $duplicate->id;
        });
    }

    public function test_duplicate_found_is_raised_when_the_user_upload_a_document_that_is_in_a_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project = tap(factory(\KBox\Project::class)->create(), function ($p) use ($user) {
            $p->users()->attach($user->id);
        });

        $manager = $project->manager;

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $manager->id
        ]);
        $service->addDocumentToGroup($manager, $descriptor, $project->collection);
        
        $duplicate = DocumentDescriptor::factory()->create([
            'hash' => $descriptor->hash,
            'owner_id' => $user->id
        ]);
        $user = $duplicate->owner;
        $file = $descriptor->file;

        Event::fake([FileDuplicateFoundEvent::class]);

        $uploadCompleteEvent = new UploadCompleted($descriptor, $user);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        Event::assertDispatched(FileDuplicateFoundEvent::class, function ($e) use ($user, $descriptor, $duplicate) {
            return $e->user->is($user) &&
                $e->duplicateDocument->duplicate_document_id === $descriptor->id &&
                $e->duplicateDocument->document_id === $duplicate->id;
        });
    }

    public function test_duplicate_found_not_dispatched_if_user_upload_again_a_old_revision_of_document()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $userForDuplicate = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $last_version = $document->file;

        $first_version = factory(\KBox\File::class)->create([
            'mime_type' => 'text/html',
            'hash' => 'new_hash'
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $duplicateFile = File::factory()->create([
            'hash' => $first_version->hash,
            'user_id' => $userForDuplicate->id
        ]);

        $duplicate = DocumentDescriptor::factory()->create([
            'hash' => $first_version->hash,
            'owner_id' => $userForDuplicate->id,
            'file_id' => $duplicateFile->id
        ]);

        Event::fake([FileDuplicateFoundEvent::class]);

        $uploadCompleteEvent = new UploadCompleted($duplicate, $userForDuplicate);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        Event::assertNotDispatched(FileDuplicateFoundEvent::class);
    }

    public function test_calculate_used_quota_job_dispatched()
    {
        Bus::fake();
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $userForDuplicate = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $last_version = $document->file;

        $first_version = factory(\KBox\File::class)->create([
            'mime_type' => 'text/html',
            'hash' => 'new_hash'
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $duplicateFile = File::factory()->create([
            'hash' => $first_version->hash,
            'user_id' => $userForDuplicate->id
        ]);

        $duplicate = DocumentDescriptor::factory()->create([
            'hash' => $first_version->hash,
            'owner_id' => $userForDuplicate->id,
            'file_id' => $duplicateFile->id
        ]);

        $uploadCompleteEvent = new UploadCompleted($duplicate, $userForDuplicate);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        Bus::assertDispatched(CalculateUserUsedQuota::class);
    }
}
