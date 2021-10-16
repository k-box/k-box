<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use KBox\User;
use KBox\Group;
use KBox\Project;
use KBox\Capability;
use Tests\TestCase;
use Illuminate\Support\Collection;
use KBox\Documents\Services\DocumentsService;

use KBox\DocumentDescriptor;
use KBox\Exceptions\ForbiddenException;

/*
 * Test something related to document descriptors management
*/
class CollectionsTest extends TestCase
{
    public function user_provider_admin_project()
    {
        return [
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
        ];
    }

    public function user_provider_for_editpage_public_checkbox_test()
    {
        return [
            [Capability::$ADMIN, true],
            [[Capability::MANAGE_KBOX], false],
            [Capability::$PROJECT_MANAGER, true],
            [Capability::$PARTNER, false],
        ];
    }

    /**
     * Test that the route for documents.show is not leaking private documents to anyone
     *
     * @return void
     */
    public function testSeePersonalCollectionLoginRequired()
    {
        $user = User::factory()->admin()->create();
        
        $service = app(DocumentsService::class);
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route('documents.groups.show', $collection->id);
        
        $this->get($url)->assertRedirect(route('frontpage'));
    }
    
    public function testSeePersonalCollectionAccessGranted()
    {
        $this->withKlinkAdapterFake();
        
        $user = User::factory()->admin()->create();
        
        $service = app(DocumentsService::class);
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route('documents.groups.show', $collection->id);
        
        // test with login with the owner user
        
        $response = $this->actingAs($user)->get($url);
        
        $response->assertOk();
    }
    
    public function testSeePersonalCollectionAccessDenieded()
    {
        $user = User::factory()->admin()->create();
        
        $user_not_owner = User::factory()->partner()->create();
        
        $service = app(DocumentsService::class);
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route('documents.groups.show', $collection->id);
        
        $response = $this->actingAs($user_not_owner)->get($url);
        $response->assertForbidden();
    }
    
    public function testCollectionListing()
    {
        $user1 = User::factory()->projectManager()->create();
        
        $user2 = User::factory()->projectManager()->create();
        
        $user_admin = User::factory()->admin()->create();
        
        // $users = [$user1, $user2];
        
        $projectA = Project::factory()->create(['user_id' => $user1->id]);
        $projectB = Project::factory()->create(['user_id' => $user1->id]);
        $projectC = Project::factory()->create(['user_id' => $user2->id]);
        
        $service = app(DocumentsService::class);
        
        $grp1 = $service->createGroup($user1, 'Personal collection of user '.$user1->id);
        $grp2 = $service->createGroup($user2, 'Personal collection of user '.$user2->id);
        
        // current expected status:
        // $user1 => $projectA + $projectB and $grp1
        // $user2 => $projectC and $grp2
        
        $collections_user1 = $service->getCollectionsAccessibleByUser($user1);
        $this->assertNotNull($collections_user1);
        $this->assertNotNull($collections_user1->personal, 'null personal of User1');
        $this->assertNotNull($collections_user1->projects, 'null projects of User1');
        $this->assertEquals(2, $collections_user1->projects->count());
        $this->assertEquals(1, $collections_user1->personal->count());
        
        $collections_user2 = $service->getCollectionsAccessibleByUser($user2);
        $this->assertNotNull($collections_user2);
        $this->assertNotNull($collections_user2->personal, 'null personal of User2');
        $this->assertNotNull($collections_user2->projects, 'null projects of User2');
        $this->assertEquals(1, $collections_user2->projects->count());
        $this->assertEquals(1, $collections_user2->personal->count());
        
        $collections_user_admin = $service->getCollectionsAccessibleByUser($user_admin);
        $this->assertNotNull($collections_user_admin);
        $this->assertNotNull($collections_user_admin->personal, 'null personal of UserAdmin');
        $this->assertNotNull($collections_user_admin->projects, 'null projects of UserAdmin');
        $this->assertEquals(3, $collections_user_admin->projects->count());
        $this->assertEquals(0, $collections_user_admin->personal->count());
        
        // User 2 is added to Project A
        $projectA->users()->save($user2);
        
        $collections_user1 = $service->getCollectionsAccessibleByUser($user1);
        $this->assertNotNull($collections_user1);
        $this->assertNotNull($collections_user1->personal);
        $this->assertNotNull($collections_user1->projects);
        $this->assertEquals(2, $collections_user1->projects->count());
        $this->assertEquals(1, $collections_user1->personal->count());
        
        $collections_user2 = $service->getCollectionsAccessibleByUser($user2);
        $this->assertNotNull($collections_user2);
        $this->assertNotNull($collections_user2->personal);
        $this->assertNotNull($collections_user2->projects);
        $this->assertEquals(2, $collections_user2->projects->count(), 'Projects collection count after user2 has been added to ProjectA');
        $this->assertEquals(1, $collections_user2->personal->count());
        
        $collections_user_admin = $service->getCollectionsAccessibleByUser($user_admin);
        $this->assertNotNull($collections_user_admin);
        $this->assertNotNull($collections_user_admin->personal);
        $this->assertNotNull($collections_user_admin->projects);
        $this->assertEquals(3, $collections_user_admin->projects->count());
        $this->assertEquals(0, $collections_user_admin->personal->count());
        
        $grp3 = $service->createGroup($user2, 'Another Personal collection of user '.$user2->id);
        
        $collections_user2 = $service->getCollectionsAccessibleByUser($user2);
        $this->assertNotNull($collections_user2);
        $this->assertNotNull($collections_user2->personal);
        $this->assertNotNull($collections_user2->projects);
        $this->assertEquals(2, $collections_user2->projects->count(), 'Projects collection count after user2 has been added to ProjectA');
        $this->assertEquals(2, $collections_user2->personal->count(), 'Personal collection final count');
    }

    /**
     * Test that the accessible collections by a user are returned
     * in alphabetical order, based on Group->$name
     *
     * @dataProvider user_provider_admin_project
     */
    public function testCollectionListingInAlphabeticalOrder($caps)
    {
        $collection_names = ['z', 'a', 'd', 'b', 'cc', 'ca', 'k'];
        $expected_collection_names = ['a', 'b', 'ca', 'cc', 'd', 'k', 'z'];

        $project_collection_names = ['pz', 'pa', 'pd', 'pb', 'pcc', 'pca', 'pk'];
        $project_expected_collection_names = ['pa', 'pb', 'pca', 'pcc', 'pd', 'pk', 'pz'];

        $user = tap(User::factory()->create(), function ($u) use ($caps) {
            $u->addCapabilities($caps);
        });

        $service = app(DocumentsService::class);

        $project = null;
        $project_group = null;
        $project_childs = count($collection_names);

        foreach ($project_collection_names as $name) {
            $project_group = $service->createGroup($user, $name, null, null, false);

            $project = Project::create([
                'name' => $name,
                'user_id' => $user->id,
                'collection_id' => $project_group->id,
            ]);

            for ($i=0; $i < $project_childs; $i++) {
                $service->createGroup($user, $project_collection_names[$i], null, $project_group, false);
            }
        }
        
        $group = null;
        $childs = count($collection_names);

        foreach ($collection_names as $name) {
            $group = $service->createGroup($user, $name, null, null, true);

            for ($i=0; $i < $childs; $i++) {
                $service->createGroup($user, $collection_names[$i], null, $group, true);
            }
        }

        // make sure no cached elements are returned
        \Cache::forget('dms_project_collections');
        \Cache::forget('dms_project_collections-'.$user->id);

        $collections = $service->getCollectionsAccessibleByUser($user);

        // Testing the personal collection tree

        $personals = $collections->personal;

        $this->assertEquals($expected_collection_names, $personals->pluck('name')->toArray());

        foreach ($personals as $sub_collection) {
            $this->assertEquals($expected_collection_names, $sub_collection->children->pluck('name')->toArray());
        }

        // testing the project collection tree

        $projects = $collections->projects;

        $this->assertEquals($project_expected_collection_names, $projects->pluck('name')->toArray());

        foreach ($projects as $sub_collection) {
            $this->assertEquals($project_expected_collection_names, $sub_collection->children->pluck('name')->toArray());
        }
    }
    
    public function testIsCollectionAccessible()
    {
        $service = app(DocumentsService::class);
        
        // create a project
        
        $project = Project::factory()->create();
        
        $user = $project->manager()->first();
        
        $project_collection = $project->collection()->first();
        
        // add a collection to it
        $collection = $service->createGroup($user, 'sub-collection name', null, $project_collection, false);
        
        // test if sub-collection is accessible by the project admin
        
        $accessible = $service->isCollectionAccessible($user, $collection);
        
        $this->assertTrue($accessible, 'Collection is not accessible by the creator');
        
        $projectA = Project::factory()->create(['user_id' => $user->id]);
        $projectB = Project::factory()->create(['user_id' => $user->id]);
        $projectC = Project::factory()->create(['user_id' => $user->id]);
        
        $collection2 = $service->createGroup($user, 'sub-sub-collection name', null, $collection, false);
        
        $accessible = $service->isCollectionAccessible($user, $collection2);
        
        $this->assertTrue($accessible, 'Collection is not accessible by the creator');
        
        $user_admin = User::factory()->admin()->create();
        
        $collection3 = $service->createGroup($user, 'by admin', null, $project_collection, false);
        
        $accessible = $service->isCollectionAccessible($user, $collection3);
        
        $this->assertTrue($accessible, 'Collection is not accessible by the creator');
        
        $partner = User::factory()->partner()->create();
        
        $accessible = $service->isCollectionAccessible($partner, $collection);
        $this->assertFalse($accessible, 'Collection 1 is accessible by Partner user not added to the project');
        $accessible = $service->isCollectionAccessible($partner, $collection2);
        $this->assertFalse($accessible, 'Collection 2 is accessible by Partner user not added to the project');
        $accessible = $service->isCollectionAccessible($partner, $collection3);
        $this->assertFalse($accessible, 'Collection 3 is accessible by Partner user not added to the project');
        
        // add 1 partner user to the project
        
        $project->users()->save($partner);
        
        $accessible = $service->isCollectionAccessible($partner, $collection);
        $this->assertTrue($accessible, 'Collection 1 is not accessible by Partner user not added to the project');
        $accessible = $service->isCollectionAccessible($partner, $collection2);
        $this->assertTrue($accessible, 'Collection 2 is not accessible by Partner user not added to the project');
        $accessible = $service->isCollectionAccessible($partner, $collection3);
        $this->assertTrue($accessible, 'Collection 3 is not accessible by Partner user not added to the project');
    }
    
    public function testCollectionCacheForUserUpdate()
    {
        $user = User::factory()->admin()->create();
        
        $service = app(DocumentsService::class);
        
        \Cache::shouldReceive('forget')
                    ->once()
                    ->with('dms_personal_collections'.$user->id)
                    ->andReturn(true);
        
        $grp1 = $service->createGroup($user, 'Personal collection of user '.$user->id);

        $this->assertTrue(true, "Test complete without exceptions");
    }
    
    public function testBulkCopyToCollection()
    {
        $user = User::factory()->admin()->create();
        
        $service = app(DocumentsService::class);
        
        // create one document
        $doc = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id
        ]);
        
        $doc2 = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id
        ]);
        
        // create one collection
        $grp1 = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        // Add doc to collection using the BulkController@copyTo method
        // This also tests if the bulk controller handles correctly the duplicates
        
        \Session::start();
        
        $this->actingAs($user);
        
        $response = $this->json('POST', route('documents.bulk.copyto'), [
            'documents' => [ $doc->id ],
            'destination_group' => $grp1->id,
            '_token' => csrf_token()
        ]);
        
        $response->assertJson([
            'status' => 'ok',
            'message' => trans('documents.bulk.copy_completed_all', ['collection' => $grp1->name]),
        ]);
        
        $this->assertEquals(1, $grp1->documents()->count());
        
        // try to add a second document and again the first document
        
        $response = $this->json('POST', route('documents.bulk.copyto'), [
            'documents' => [ $doc->id, $doc2->id ],
            'destination_group' => $grp1->id,
            '_token' => csrf_token()
        ]);
        
        $response->assertJson([
            'status' => 'partial',
            'message' => trans_choice('documents.bulk.copy_completed_some', 1, ['count' => 1, 'collection' => $grp1->name, 'remaining' => 1]),
        ]);
        
        $this->assertEquals(2, $grp1->documents()->count());
    }
    
    public function testDmsCollectionsCleanDuplicatesCommand()
    {
        $user = User::factory()->admin()->create();
        
        $service = app(DocumentsService::class);
        
        // create one document
        $doc = DocumentDescriptor::factory()->create([
            'owner_id' => $user->id
        ]);
        
        // create one collection
        $grp1 = $service->createGroup($user, 'Personal collection of user '.$user->id);
        $grp2 = $service->createGroup($user, 'Another collection of user '.$user->id);
        
        $service->addDocumentsToGroup($user, Collection::make([$doc]), $grp1, false);
        $service->addDocumentsToGroup($user, Collection::make([$doc]), $grp1, false);
        $service->addDocumentsToGroup($user, Collection::make([$doc]), $grp1, false);
        
        $this->assertEquals(3, $grp1->documents()->count());
        $this->assertEquals(0, $grp2->documents()->count());
        
        $exitCode = \Artisan::call('collections:clean-duplicates', [
            'collection' => $grp1->id,
            '--yes' => true,
            '--no-interaction' => true,
        ]);
        
        $this->assertEquals(0, $exitCode);
        
        $this->assertEquals(1, $grp1->documents()->count());
        $this->assertEquals(0, $grp2->documents()->count());
    }

    public function testDocumentService_deleteGroup()
    {
        $user = User::factory()->partner()->create();

        $group = $this->createCollection($user, true, 3);

        // get childs
        $children = $group->getChildren();

        $children_ids = $children->pluck('id')->toArray();

        $this->assertEquals(3, $children->count(), 'Children count pre-condition');

        $service = app(DocumentsService::class);

        $is_deleted = $service->deleteGroup($user, $group);

        $this->assertTrue($is_deleted);

        $group = Group::withTrashed()->findOrFail($group->id);

        $trashed_children = Group::withTrashed()->whereIn('id', $children_ids)->get();

        $after_delete_children = $group->getChildren();

        $this->assertTrue($group->trashed());

        // assert all childs are trashed

        $this->assertEquals(0, $after_delete_children->count());
        $this->assertEquals(3, $trashed_children->count());
    }

    public function testDocumentService_deleteGroup_forbidden()
    {
        $user = User::factory()->partner()->create();
        $user2 = User::factory()->partner()->create();

        $doc = $this->createCollection($user);

        $service = app(DocumentsService::class);

        $this->expectException(ForbiddenException::class);

        $service->deleteGroup($user2, $doc);
    }

    public function testCollectionDelete()
    {
        $user = User::factory()->partner()->create();

        $doc = $this->createCollection($user);

        \Session::start();

        $url = route('documents.groups.destroy', ['group' => $doc->id,
                '_token' => csrf_token()]);
        
        $this->actingAs($user);

        $response = $this->delete($url);

        $response->assertSee('ok');
        
        $response->assertStatus(202);

        $doc = Group::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());
    }

    public function testDocumentService_permanentlyDeleteGroup()
    {
        $user = User::factory()->projectManager()->create();
        
        $group = $this->createCollection($user, true, 3);

        // get childs
        $children = $group->getChildren();

        $children_ids = $children->pluck('id')->toArray();

        $this->assertEquals(3, $children->count(), 'Children count pre-condition');

        $service = app(DocumentsService::class);

        $service->deleteGroup($user, $group); // put doc in trash
        
        $group = Group::withTrashed()->findOrFail($group->id);

        $is_deleted = $service->permanentlyDeleteGroup($group, $user);
        
        $this->assertTrue($is_deleted);

        $exists_doc = Group::withTrashed()->find($group->id);

        $this->assertNull($exists_doc);

        $after_delete_children = $group->getChildren();

        $trashed_children = Group::withTrashed()->whereIn('id', $children_ids)->get();

        // assert all childs are trashed
        $this->assertEquals(0, $after_delete_children->count());
    }

    public function testDocumentService_permanentlyDeleteGroup_forbidden()
    {
        $user = User::factory()->projectManager()->create();
        
        $user2 = User::factory()->partner()->create();
        
        $group = $this->createCollection($user, false);

        $group->delete();

        $service = app(DocumentsService::class);

        $this->expectException(ForbiddenException::class);

        $is_deleted = $service->permanentlyDeleteGroup($group, $user2);
    }

    public function testGroupForceDelete()
    {
        $user = User::factory()->projectManager()->create();

        $group = $this->createCollection($user);

        \Session::start();

        $url = route('documents.groups.destroy', ['group' => $group->id,
                '_token' => csrf_token()]);
        
        $this->actingAs($user);

        $response = $this->delete($url);

        $response->assertSee('ok');
        
        $response->assertStatus(202);

        $url = route('documents.groups.destroy', [
                'group' => $group->id,
                'force' => true,
                '_token' => csrf_token()]);

        $response = $this->delete($url);

        $response->assertSee('ok');
        
        $response->assertStatus(202);

        $this->expectException(ModelNotFoundException::class);

        $doc = Group::withTrashed()->findOrFail($group->id);
    }

    protected function createCollection(User $user, $is_personal = true, $childs = 0)
    {
        $service = app(DocumentsService::class);

        $group = $service->createGroup($user, 'collection of user '.$user->id, null, null, $is_personal);

        if ($childs > 0) {
            for ($i=0; $i < $childs; $i++) {
                $service->createGroup($user, 'Child '.$user->id.'-'.$group->id.'-'.$i, null, $group, $is_personal);
            }
        }

        return $group;
    }
}
