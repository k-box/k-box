<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Group;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Klink\DmsAdapter\KlinkVisibilityType;
use KBox\Project;
use Illuminate\Support\Facades\Event;
use KBox\DocumentGroups;
use KBox\Documents\Services\DocumentsService;
use KBox\Events\DocumentsAddedToCollection;
use KBox\Events\DocumentsRemovedFromCollection;
use KBox\File;
use KBox\Shared;

/*
 * Test something related to document descriptors management
*/
class DocumentsServiceTest extends TestCase
{
    public function user_provider_no_guest()
    {
        return [
            [Capability::$ADMIN, 'admin'],
            [Capability::$PROJECT_MANAGER, 'project_manager'],
            [Capability::$PARTNER, 'partner'],
        ];
    }

    /**
     * @dataProvider user_provider_no_guest
     */
    public function testGetDocumentCollections($caps)
    {
        $adapter = $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $user = $this->createUser($caps);
        
        $doc = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id
        ]);

        $owned_project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $other_project = Project::factory()->create();

        $secondary_project = Project::factory()->create();

        $secondary_project->users()->save($user);
        
        $group = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        $group->documents()->save($doc);

        Group::findOrFail($owned_project->collection_id)->documents()->save($doc);

        Group::findOrFail($secondary_project->collection_id)->documents()->save($doc);

        // simulate another user, who has access to both projects, that
        // is adding the document to the second project
        Group::findOrFail($other_project->collection_id)->documents()->save($doc);
        
        // now get the collections of the doc
        $collections = $service->getDocumentCollections($doc, $user);

        $this->assertEquals($user->isDMSManager() ? 4 : 3, $collections->count());
    }

    public function testFallbackDocumentReIndexingAfterForceHashCheckOnKCore()
    {
        $this->withKlinkAdapterFake();

        // todo test add and reindex
        $user = $this->createUser(Capability::$ADMIN);

        $descr = $this->createDocument($user);
        
        $file = $descr->file;

        // change the document to a complete non-sense mime-type to
        // force a fallback text extraction
        $descr->mime_type = 'text/basiliscus';
        $file->mime_type = 'text/basiliscus';
        $file->save();
        $file = $file->fresh();
        $descr->save();
        $descr = $descr->fresh();

        $service = app('KBox\Documents\Services\DocumentsService');

        $ret = $service->reindexDocument($descr, 'private');

        $this->assertInstanceOf(DocumentDescriptor::class, $ret);
    }

    public function testFallbackDocumentIndexingAfterForceHashCheckOnKCore()
    {
        $this->withKlinkAdapterFake();

        // todo test add and reindex
        $user = $this->createUser(Capability::$ADMIN);
        
        $file = File::factory()->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);

        // change the document to a complete non-sense mime-type to
        // force a fallback text extraction

        $file->mime_type = 'text/basiliscus';
        $file->save();
        $file = $file->fresh();

        $service = app('KBox\Documents\Services\DocumentsService');

        $ret = $service->indexDocument($file, 'private', $user);

        $this->assertInstanceOf(DocumentDescriptor::class, $ret);
    }

    /**
     * Test removing a private and public document from the public network
     */
    public function testDeletePublicDocument()
    {
        $fake = $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $user = $this->createUser(Capability::$ADMIN);

        $descr = $this->createDocument($user);
        $service->reindexDocument($descr);

        $descr->is_public = true;
        $descr->save();
        $service->reindexDocument($descr, KlinkVisibilityType::KLINK_PUBLIC);

        $fake->assertDocumentIndexed($descr->uuid, 2);

        $this->assertNotNull($fake->getDocument($descr->uuid, 'public'), 'Null public get');
        $this->assertNotNull($fake->getDocument($descr->uuid, 'private'), 'Null private get');

        $descr->is_public = false;
        $descr->save();

        $service->deletePublicDocument($descr);

        $fake->assertDocumentRemoved($descr->uuid, 'public', 1);
        $fake->assertDocumentRemoved($descr->uuid, 'private', 0);

        $this->assertNull($fake->getDocument($descr->uuid, 'public'), 'NOT Null public get');
        $this->assertNotNull($fake->getDocument($descr->uuid, 'private'), 'Null private get');
    }

    public function test_bulk_add_to_collection_attaches_user()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = User::factory()->admin()->create();

        $documents = DocumentDescriptor::factory()->count(3)->create([
            'owner_id' => $user->id,
        ]);

        $collection = Group::factory()->create([
            'user_id' => $user->id,
        ]);

        Event::fake([
            DocumentsAddedToCollection::class,
        ]);

        $service = app(DocumentsService::class);
        $service->addDocumentsToGroup($user, $documents, $collection, false);

        $documents->each(function ($document) use ($collection, $user) {
            $applied_collection = $document->fresh()->groups()->first();
    
            $this->assertNotNull($applied_collection->is($collection));
            $this->assertInstanceOf(DocumentGroups::class, $applied_collection->pivot);
            $this->assertNotNull($applied_collection->pivot->created_at);
            $this->assertNotNull($applied_collection->pivot->updated_at);
            $this->assertTrue($applied_collection->pivot->addedBy->is($user));
        });

        Event::assertDispatched(DocumentsAddedToCollection::class, function ($e) use ($collection, $documents, $user) {
            $noDifferenceBetweenDocuments = $documents->pluck('id')->diff($e->documents)->isEmpty();

            return $e->collection->is($collection)
                && $noDifferenceBetweenDocuments
                && $e->user->is($user);
        });
    }

    public function test_add_document_to_collection()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = User::factory()->admin()->create();

        $document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
        ]);

        $collection = Group::factory()->create([
            'user_id' => $user->id,
        ]);

        Event::fake([
            DocumentsAddedToCollection::class,
        ]);

        $service = app(DocumentsService::class);
        $service->addDocumentToGroup($user, $document, $collection, false);

        $applied_collection = $document->fresh()->groups()->first();

        $this->assertNotNull($applied_collection->is($collection));
        $this->assertInstanceOf(DocumentGroups::class, $applied_collection->pivot);
        $this->assertNotNull($applied_collection->pivot->created_at);
        $this->assertNotNull($applied_collection->pivot->updated_at);
        $this->assertTrue($applied_collection->pivot->addedBy->is($user));
        
        Event::assertDispatched(DocumentsAddedToCollection::class, function ($e) use ($collection, $document, $user) {
            return $e->collection->is($collection)
                && count($e->documents) === 1
                && $e->documents[0] === $document->getKey()
                && $e->user->is($user);
        });
    }

    public function test_remove_document_from_collection()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = User::factory()->admin()->create();

        $document = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
        ]);

        $collection = Group::factory()->create([
            'user_id' => $user->id,
        ]);

        $collection->documents()->save($document, ['added_by' => $user->getKey()]);

        Event::fake([
            DocumentsRemovedFromCollection::class,
        ]);

        $service = app(DocumentsService::class);
        $service->removeDocumentFromGroup($user, $document, $collection, false);

        $applied_collections = $document->fresh()->groups()->count();

        $this->assertEquals(0, $applied_collections);
        
        Event::assertDispatched(DocumentsRemovedFromCollection::class, function ($e) use ($collection, $document, $user) {
            return $e->collection->is($collection)
                && count($e->documents) === 1
                && $e->documents[0] === $document->getKey()
                && $e->user->is($user);
        });
    }

    public function test_remove_documents_from_collection()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = User::factory()->admin()->create();

        $documents = DocumentDescriptor::factory()->count(3)->create([
            'owner_id' => $user->id,
        ]);

        $collection = Group::factory()->create([
            'user_id' => $user->id,
        ]);

        $documents->each(function ($document) use ($collection, $user) {
            $collection->documents()->save($document, ['added_by' => $user->getKey()]);
        });

        Event::fake([
            DocumentsRemovedFromCollection::class,
        ]);

        $service = app(DocumentsService::class);
        $service->removeDocumentsFromGroup($user, $documents, $collection, false);

        $documents_in_collection = $collection->fresh()->documents()->count();

        $this->assertEquals(0, $documents_in_collection);
        
        Event::assertDispatched(DocumentsRemovedFromCollection::class, function ($e) use ($collection, $documents, $user) {
            $noDifferenceBetweenDocuments = $documents->pluck('id')->diff($e->documents)->isEmpty();

            return $e->collection->is($collection)
                && $noDifferenceBetweenDocuments
                && $e->user->is($user);
        });
    }

    public function test_shared_collections_can_be_listed_in_tree_view()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();
        
        $collection_creator = User::factory()->partner()->create();

        $service = app(DocumentsService::class);
        
        $single_root_collection = Group::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'name' => 'root',
        ]);
        
        $root_collection = Group::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'name' => 'root',
        ]);

        $single_sub_collection = $service->createGroup($collection_creator, 'under', null, $root_collection);
        
        $hierarchy_root_collection = Group::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'name' => 'root',
        ]);

        $hierarchy_sub_collection = $service->createGroup($collection_creator, 'under', null, $hierarchy_root_collection);
        
        $first_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $single_root_collection->getKey(),
        ]);
        
        $second_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $single_sub_collection->getKey(),
        ]);
        
        $third_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $hierarchy_root_collection->getKey(),
        ]);
        
        $fourth_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $hierarchy_sub_collection->getKey(),
        ]);

        $result = $service->getCollectionsAccessibleByUser($user);

        $this->assertEquals(3, $result->shared->count());
        $this->assertNotEmpty($result->shared->last()->children);
    }

    public function test_shared_collections_can_be_listed_in_tree_view_when_a_shareable_is_trashed()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();
        
        $collection_creator = User::factory()->partner()->create();

        $service = app(DocumentsService::class);
        
        $single_root_collection = Group::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'name' => 'root',
        ]);
        
        $root_collection = Group::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'name' => 'root',
        ]);

        $single_sub_collection = $service->createGroup($collection_creator, 'under', null, $root_collection);
        
        $hierarchy_root_collection = Group::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'name' => 'root',
        ]);

        $hierarchy_sub_collection = $service->createGroup($collection_creator, 'under', null, $hierarchy_root_collection);
        
        $first_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $single_root_collection->getKey(),
        ]);

        $single_root_collection->delete();
        
        $second_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $single_sub_collection->getKey(),
        ]);
        
        $third_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $hierarchy_root_collection->getKey(),
        ]);
        
        $fourth_share = Shared::factory()->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $hierarchy_sub_collection->getKey(),
        ]);

        $result = $service->getCollectionsAccessibleByUser($user);

        $this->assertEquals(2, $result->shared->count());
        $this->assertNotEmpty($result->shared->last()->children);
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(User::factory()->create($userParams))->addCapabilities($capabilities);
    }

    private function createDocument(User $user, $visibility = 'private')
    {
        return DocumentDescriptor::factory()->create([
            'owner_id' => $user->id,
            'visibility' => $visibility,
        ]);
    }
}
