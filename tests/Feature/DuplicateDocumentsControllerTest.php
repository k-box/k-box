<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Capability;
use KBox\DuplicateDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DuplicateDocumentsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function createDuplicates($user, $count = 1, $options = [])
    {
        return factory(DuplicateDocument::class, $count)->create($options);
    }

    public function test_duplicate_resolution_is_only_available_to_the_authenticated_users()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->json('DELETE', route('duplicates.destroy', ['id' => 1]));

        $response->assertStatus(401);
    }
    
    public function test_duplicate_resolution_is_only_available_to_the_user_that_created_the_duplicate()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicate = factory(DuplicateDocument::class)->create();

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', ['id' => $duplicate->id]));

        $response->assertStatus(403);
    }

    public function test_duplicate_is_resolved_with_original_copy()
    {
        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();

        $existing = $duplicate->duplicateOf;
        $duplicateDocument = $duplicate->document;

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', ['id' => $duplicate->id]));

        $response->assertStatus(200);

        $this->assertTrue($duplicateDocument->fresh()->trashed(), "Duplicate document not trashed");
        $this->assertTrue($duplicate->fresh()->resolved, "Duplicate not marked as resolved");
    }

    public function test_existing_document_inherits_accessible_collections_when_resolving_duplicate()
    {
        $this->disableExceptionHandling();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $adapter = $this->withKlinkAdapterFake();

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
        $duplicateDescriptor = factory('KBox\DocumentDescriptor')->create([
            'owner_id' => $manager->id,
            'hash' => $descriptor->hash
        ]);
        
        $collection_root = $service->createGroup($user, 'collection_level_one', null, null, true);
        $collection_level_one = $service->createGroup($user, 'collection_level_one', null, $collection_root, true);

        $collection_level_one->documents()->save($duplicateDescriptor);

        $service->addDocumentToGroup($manager, $descriptor, $project->collection);
        
        $duplicate = factory(DuplicateDocument::class)->create([
            'user_id' => $user->id,
            'duplicate_document_id' => $duplicateDescriptor->id,
            'document_id' => $descriptor->id,
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', ['id' => $duplicate->id]));

        $response->assertStatus(200);

        $this->assertTrue($duplicateDescriptor->fresh()->trashed(), "Duplicate document not trashed");
        $this->assertTrue($duplicate->fresh()->resolved, "Duplicate not marked as resolved");
        $this->assertEquals([$project->collection->id, $collection_level_one->id], $descriptor->fresh()->groups->pluck('id')->toArray());
    }

    public function test_trashed_document_cannot_be_used_for_resolution()
    {
        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();

        $duplicate->document->delete();

        $existing = $duplicate->duplicateOf;
        $duplicateDocument = $duplicate->document;

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', ['id' => $duplicate->id]));

        $response->assertStatus(400);

        $response->assertJson([
            'status' => 'error',
            'message' => trans('documents.duplicates.errors.resolve_with_trashed_document'),
        ]);
    }

    public function test_already_resolved_duplicates_cannot_be_resolved_again()
    {
        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();
        $duplicate->resolved = true;
        $duplicate->save();

        $existing = $duplicate->duplicateOf;
        $duplicateDocument = $duplicate->document;

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', ['id' => $duplicate->id]));

        $response->assertStatus(400);

        $response->assertJson([
            'status' => 'error',
            'message' => trans('documents.duplicates.errors.already_resolved'),
        ]);
    }
}
