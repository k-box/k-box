<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\DuplicateDocument;

use KBox\DocumentDescriptor;
use KBox\Group;

class DuplicateDocumentsControllerTest extends TestCase
{
    private function createDuplicates($user, $count = 1, $options = [])
    {
        return factory(DuplicateDocument::class, $count)->create($options);
    }

    public function test_duplicate_resolution_is_only_available_to_the_authenticated_users()
    {
        $user = factory(\KBox\User::class)->partner()->create();
        
        $response = $this->json('DELETE', route('duplicates.destroy', 1));

        $response->assertStatus(401);
    }
    
    public function test_duplicate_resolution_is_only_available_to_the_user_that_created_the_duplicate()
    {
        $user = factory(\KBox\User::class)->partner()->create();
        
        $duplicate = factory(DuplicateDocument::class)->create();

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(403);
    }

    public function test_duplicate_is_resolved_with_original_copy()
    {
        $adapter = $this->withKlinkAdapterFake();

        $user = factory(\KBox\User::class)->partner()->create();
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();

        $existing = $duplicate->duplicateOf;
        $duplicateDocument = $duplicate->document;

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(200);

        $this->assertTrue($duplicateDocument->fresh()->trashed(), "Duplicate document not trashed");
        $this->assertTrue($duplicate->fresh()->resolved, "Duplicate not marked as resolved");
    }

    public function test_existing_document_inherits_accessible_collections_when_resolving_duplicate()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $adapter = $this->withKlinkAdapterFake();

        $user = factory(\KBox\User::class)->partner()->create();

        $project = tap(factory(\KBox\Project::class)->create(), function ($p) use ($user) {
            $p->users()->attach($user->id);
        });

        $manager = $project->manager;

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $manager->id
        ]);
        $duplicateDescriptor = factory(\KBox\DocumentDescriptor::class)->create([
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

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(200);

        $this->assertTrue($duplicateDescriptor->fresh()->trashed(), "Duplicate document not trashed");
        $this->assertTrue($duplicate->fresh()->resolved, "Duplicate not marked as resolved");
        $this->assertEquals([$project->collection->id, $collection_level_one->id], $descriptor->fresh()->groups->pluck('id')->toArray());
    }

    public function test_trashed_document_cannot_be_used_for_resolution()
    {
        $adapter = $this->withKlinkAdapterFake();

        $user = factory(\KBox\User::class)->partner()->create();
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();

        $duplicate->document->delete();

        $existing = $duplicate->duplicateOf;
        $duplicateDocument = $duplicate->document;

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(400);

        $response->assertJson([
            'status' => 'error',
            'message' => trans('documents.duplicates.errors.resolve_with_trashed_document'),
        ]);
    }

    public function test_already_resolved_duplicates_cannot_be_resolved_again()
    {
        $adapter = $this->withKlinkAdapterFake();

        $user = factory(\KBox\User::class)->partner()->create();
        
        $duplicate = $this->createDuplicates($user, 1, ['user_id' => $user->id])->first();
        $duplicate->resolved = true;
        $duplicate->save();

        $existing = $duplicate->duplicateOf;
        $duplicateDocument = $duplicate->document;

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(400);

        $response->assertJson([
            'status' => 'error',
            'message' => trans('documents.duplicates.errors.already_resolved'),
        ]);
    }

    public function test_duplicate_in_same_collection_can_be_resolved()
    {
        $adapter = $this->withKlinkAdapterFake();

        $user = factory(\KBox\User::class)->partner()->create();

        $collection = Group::factory()->create([
            'user_id' => $user->id,
            // 'is_private' => true,
        ]);

        $existing_document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
        ]);

        $duplicate_document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
        ]);

        $collection->documents()->save($existing_document);
        $collection->documents()->save($duplicate_document);

        $duplicate = factory(DuplicateDocument::class)->create([
            'user_id' => $user->id,
            'document_id' => $existing_document->id,
            'duplicate_document_id' => $duplicate_document->id,
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(200);

        $this->assertTrue($duplicate_document->fresh()->trashed(), "Duplicate document not trashed");
        $this->assertTrue($duplicate->fresh()->resolved, "Duplicate not marked as resolved");
        $this->assertEquals(1, $collection->documents()->withTrashed()->where('document_id', $existing_document->id)->count(), "duplicate entry in document->collection relation for the existing file");
    }

    public function test_duplicate_in_different_collections_can_be_resolved()
    {
        $adapter = $this->withKlinkAdapterFake();

        $user = factory(\KBox\User::class)->partner()->create();

        $collection_for_existing = Group::factory()->create([
            'user_id' => $user->id,
            // 'is_private' => true,
        ]);
        
        $collection_for_duplicate = Group::factory()->create([
            'user_id' => $user->id,
            // 'is_private' => true,
        ]);

        $existing_document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
        ]);

        $duplicate_document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
        ]);

        $collection_for_existing->documents()->save($existing_document);
        $collection_for_duplicate->documents()->save($duplicate_document);

        $duplicate = factory(DuplicateDocument::class)->create([
            'user_id' => $user->id,
            'document_id' => $existing_document->id,
            'duplicate_document_id' => $duplicate_document->id,
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('duplicates.destroy', $duplicate->id));

        $response->assertStatus(200);

        $this->assertTrue($duplicate_document->fresh()->trashed(), "Duplicate document not trashed");
        $this->assertTrue($duplicate->fresh()->resolved, "Duplicate not marked as resolved");

        $found_collections = $existing_document->fresh()->groups()->pluck('group_id')->toArray();
        
        $this->assertCount(2, $found_collections);
        $this->assertContains($collection_for_existing->id, $found_collections);
        $this->assertContains($collection_for_duplicate->id, $found_collections);
    }
}
