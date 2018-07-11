<?php

namespace Tests\Unit;

use KBox\File;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Jobs\ElaborateDocument;
use KBox\Events\UploadCompleted;
use Tests\Concerns\ClearDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use KBox\Events\FileDuplicateFoundEvent;
use KBox\Listeners\UploadCompletedHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UploadCompletedHandlerTest extends TestCase
{
    use DatabaseTransactions, ClearDatabase;

    public function test_elaborate_document_is_dispatched()
    {
        Queue::fake();
        $descriptor = factory(DocumentDescriptor::class)->create();
        $user = $descriptor->owner;

        $uploadCompleteEvent = new UploadCompleted($descriptor, $user);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        Queue::assertPushed(ElaborateDocument::class, function ($job) use ($descriptor) {
            return $job->descriptor->id === $descriptor->id;
        });
    }

    public function test_descriptor_status_is_updated()
    {
        Queue::fake();
        $descriptor = factory(DocumentDescriptor::class)->create([
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
        Event::fake();
        $descriptor = factory(DocumentDescriptor::class)->create();
        $user = $descriptor->owner;

        $duplicate = factory(DocumentDescriptor::class)->create([
            'hash' => $descriptor->hash,
            'owner_id' => $user->id
        ]);
        $file = $descriptor->file;

        $uploadCompleteEvent = new UploadCompleted($descriptor, $user);

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
        $this->disableExceptionHandling();
        Event::fake();
        $this->withKlinkAdapterFake();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project = tap(factory('KBox\Project')->create(), function ($p) use ($user) {
            $p->users()->attach($user->id);
        });

        $manager = $project->manager;

        $descriptor = factory('KBox\DocumentDescriptor')->create([
            'owner_id' => $manager->id
        ]);
        $service->addDocumentToGroup($manager, $descriptor, $project->collection);
        
        
        $duplicate = factory(DocumentDescriptor::class)->create([
            'hash' => $descriptor->hash,
            'owner_id' => $user->id
        ]);
        $user = $duplicate->owner;
        $file = $descriptor->file;

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
        $this->disableExceptionHandling();
        Event::fake();
        $this->withKlinkAdapterFake();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $userForDuplicate = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
            'hash' => 'new_hash'
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $duplicateFile = factory(File::class)->create([
            'hash' => $first_version->hash,
            'user_id' => $userForDuplicate->id
        ]);

        $duplicate = factory(DocumentDescriptor::class)->create([
            'hash' => $first_version->hash,
            'owner_id' => $userForDuplicate->id,
            'file_id' => $duplicateFile->id
        ]);

        $uploadCompleteEvent = new UploadCompleted($duplicate, $userForDuplicate);

        $handler = new UploadCompletedHandler();

        $handler->handle($uploadCompleteEvent);

        Event::assertNotDispatched(FileDuplicateFoundEvent::class);
    }
}

// document of one user, access with the other
// $user = tap(factory('KBox\User')->create(), function ($u) {
//     $u->addCapabilities(Capability::$PROJECT_MANAGER);
// });
// $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
//     $u->addCapabilities(Capability::$PARTNER);
// });

// $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id]);
