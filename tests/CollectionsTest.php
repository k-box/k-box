<?php

use Laracasts\TestDummy\Factory;
use KBox\User;
use KBox\Group;
use KBox\Project;
use KBox\Capability;
use Illuminate\Support\Collection;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test something related to document descriptors management
*/
class CollectionsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function user_provider_admin_project()
    {
        return [
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
            [Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH],
        ];
    }

    public function user_provider_for_editpage_public_checkbox_test()
    {
        return [
            [Capability::$ADMIN, true],
            [Capability::$DMS_MASTER, false],
            [Capability::$PROJECT_MANAGER, true],
            [Capability::$PARTNER, false],
            [Capability::$GUEST, false],
        ];
    }

    /**
     * Test that the route for documents.show is not leaking private documents to anyone
     *
     * @return void
     */
    public function testSeePersonalCollectionLoginRequired()
    {
        // create a document
        
        $user = $this->createAdminUser();
        
        // $user_not_owner = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route('documents.groups.show', $collection->id);
        
        $this->visit($url)->seePageIs(route('frontpage'));
    }
    
    public function testSeePersonalCollectionAccessGranted()
    {
        // create a document

        $this->withKlinkAdapterFake();
        
        $user = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route('documents.groups.show', $collection->id);
        
        // test with login with the owner user
        
        $this->actingAs($user);
        
        $this->visit($url)->seePageIs($url);
        
        $this->assertResponseOk();
    }
    
    public function testSeePersonalCollectionAccessDenieded()
    {
        // create a document
        
        $user = $this->createAdminUser();
        
        $user_not_owner = $this->createUser(Capability::$PARTNER);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route('documents.groups.show', $collection->id);
        
        // test with login with another user
        
        $this->actingAs($user_not_owner);
        
        $this->call('GET', $url);
        
        $this->assertResponseStatus(403);
    }
    
    public function testCollectionListing()
    {
        $user1 = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        
        $user2 = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        
        $user_admin = $this->createAdminUser();
        
        // $users = [$user1, $user2];
        
        $projectA = factory(\KBox\Project::class)->create(['user_id' => $user1->id]);
        $projectB = factory(\KBox\Project::class)->create(['user_id' => $user1->id]);
        $projectC = factory(\KBox\Project::class)->create(['user_id' => $user2->id]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
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

        $user = $this->createUser($caps);
        
        $service = app('Klink\DmsDocuments\DocumentsService');

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
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        // create a project
        
        $project = factory(\KBox\Project::class)->create();
        
        $user = $project->manager()->first();
        
        $project_collection = $project->collection()->first();
        
        // add a collection to it
        $collection = $service->createGroup($user, 'sub-collection name', null, $project_collection, false);
        
        // test if sub-collection is accessible by the project admin
        
        $accessible = $service->isCollectionAccessible($user, $collection);
        
        $this->assertTrue($accessible, 'Collection is not accessible by the creator');
        
        $projectA = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
        $projectB = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
        $projectC = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
        
        $collection2 = $service->createGroup($user, 'sub-sub-collection name', null, $collection, false);
        
        $accessible = $service->isCollectionAccessible($user, $collection2);
        
        $this->assertTrue($accessible, 'Collection is not accessible by the creator');
        
        $user_admin = $this->createAdminUser();
        
        $collection3 = $service->createGroup($user, 'by admin', null, $project_collection, false);
        
        $accessible = $service->isCollectionAccessible($user, $collection3);
        
        $this->assertTrue($accessible, 'Collection is not accessible by the creator');
        
        $partner = $this->createUser(Capability::$PARTNER);
        
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
        $user = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        \Cache::shouldReceive('forget')
                    ->once()
                    ->with('dms_personal_collections'.$user->id)
                    ->andReturn(true);
        
        $grp1 = $service->createGroup($user, 'Personal collection of user '.$user->id);

        $this->assertTrue(true, "Test complete without exceptions");
    }
    
    public function testBulkCopyToCollection()
    {
        $user = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        // create one document
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id
        ]);
        
        $doc2 = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id
        ]);
        
        // create one collection
        $grp1 = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        // Add doc to collection using the BulkController@copyTo method
        // This also tests if the bulk controller handles correctly the duplicates
        
        \Session::start();
        
        $this->actingAs($user);
        
        $this->json('POST', route('documents.bulk.copyto'), [
            'documents' => [ $doc->id ],
            'destination_group' => $grp1->id,
            '_token' => csrf_token()
        ])->seeJson([
            'status' => 'ok',
            'message' => trans('documents.bulk.copy_completed_all', ['collection' => $grp1->name]),
        ]);
        
        $this->assertEquals(1, $grp1->documents()->count());
        
        // try to add a second document and again the first document
        
        $this->json('POST', route('documents.bulk.copyto'), [
            'documents' => [ $doc->id, $doc2->id ],
            'destination_group' => $grp1->id,
            '_token' => csrf_token()
        ])->seeJson([
            'status' => 'partial',
            'message' => trans_choice('documents.bulk.copy_completed_some', 1, ['count' => 1, 'collection' => $grp1->name, 'remaining' => 1]),
        ]);
        
        $this->assertEquals(2, $grp1->documents()->count());
    }
    
    public function testDmsCollectionsCleanDuplicatesCommand()
    {
        $user = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        // create one document
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
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
        $user = $this->createUser(Capability::$PARTNER);

        $group = $this->createCollection($user, true, 3);

        // get childs
        $children = $group->getChildren();

        $children_ids = $children->pluck('id')->toArray();

        $this->assertEquals(3, $children->count(), 'Children count pre-condition');

        $service = app('Klink\DmsDocuments\DocumentsService');

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

    /**
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testDocumentService_deleteGroup_forbidden()
    {
        $user = $this->createUser(Capability::$PARTNER);
        $user2 = $this->createUser(Capability::$PARTNER);

        $doc = $this->createCollection($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->deleteGroup($user2, $doc);
    }

    public function testCollectionDelete()
    {
        $user = $this->createUser(Capability::$PARTNER);

        $doc = $this->createCollection($user);

        \Session::start();

        $url = route('documents.groups.destroy', ['id' => $doc->id,
                '_token' => csrf_token()]);
        
        $this->actingAs($user);

        $this->delete($url);

        $this->see('ok');
        
        $this->assertResponseStatus(202);

        $doc = Group::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());
    }

    public function testDocumentService_permanentlyDeleteGroup()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $group = $this->createCollection($user, true, 3);

        // get childs
        $children = $group->getChildren();

        $children_ids = $children->pluck('id')->toArray();

        $this->assertEquals(3, $children->count(), 'Children count pre-condition');

        $service = app('Klink\DmsDocuments\DocumentsService');

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

    /**
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testDocumentService_permanentlyDeleteGroup_forbidden()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);
        $user2 = $this->createUser(Capability::$PARTNER);

        $group = $this->createCollection($user, false);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->deleteGroup($user, $group); // put doc in trash

        $is_deleted = $service->permanentlyDeleteDocument($group, $user2);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testGroupForceDelete()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $doc = $this->createCollection($user);

        \Session::start();

        $url = route('documents.groups.destroy', ['id' => $doc->id,
                '_token' => csrf_token()]);
        
        $this->actingAs($user);

        $this->delete($url);

        $this->see('ok');
        
        $this->assertResponseStatus(202);

        $url = route('documents.groups.destroy', [
                'id' => $doc->id,
                'force' => true,
                '_token' => csrf_token()]);

        $this->delete($url);

        $this->see('ok');
        
        $this->assertResponseStatus(202);

        $doc = Group::withTrashed()->findOrFail($doc->id);
    }
}
