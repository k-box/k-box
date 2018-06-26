<?php

namespace Tests\Unit;

use KBox\Group;
use Tests\TestCase;
use KBox\Capability;
use KBox\Exceptions\ForbiddenException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_trashing_personal_collections_trash_also_descendants()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
    
        //create a hierarchy
        $collection_root = $service->createGroup($user, 'collection_level_one', null, null, true);
        $collection_level_one = $service->createGroup($user, 'collection_level_one', null, $collection_root, true);
        $collection_level_two = $service->createGroup($user, 'collection_level_two', null, $collection_root, true);
        $collection_level_three = $service->createGroup($user, 'collection_level_three', null, $collection_level_one, true);
        $collection_level_four = $service->createGroup($user, 'collection_level_four', null, $collection_level_three, true);

        $this->assertEquals(4, $collection_root->getDescendants()->count());

        // delete a collection close to the top

        $trashed = $service->deleteGroup($user, $collection_level_one);

        // assert that target collection and all sub-collections are deleted

        $collection = $collection_root->fresh();

        $this->assertTrue($trashed);
        $this->assertEquals(1, $collection->getDescendants()->count());
    }

    public function test_trashing_project_collection_trash_also_descendants()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $manager = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        });
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory('KBox\Project')->create([
            'user_id' => $manager->id
        ]);

        $project->users()->attach($user);
            
        //create a hierarchy
        $collection_level_one = $service->createGroup($manager, 'collection_level_one', null, $project->collection, false);
        $collection_level_two = $service->createGroup($manager, 'collection_level_two', null, $project->collection, false);
        $collection_level_three = $service->createGroup($manager, 'collection_level_three', null, $collection_level_one, false);
        $collection_level_four = $service->createGroup($manager, 'collection_level_four', null, $collection_level_three, false);

        $this->assertEquals(4, $project->collection->getDescendants()->count());

        // delete a collection close to the top

        $trashed = $service->deleteGroup($user, $collection_level_one);

        // assert that target collection and all sub-collections are deleted

        $collection = $project->collection->fresh();

        $this->assertTrue($trashed);
        $this->assertEquals(1, $collection->getDescendants()->count());
    }

    public function test_manager_can_permanently_delete_collections_in_project()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $manager = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        });
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory('KBox\Project')->create([
            'user_id' => $manager->id
        ]);
        $project->users()->attach($user);
            
        //create a hierarchy
        $collection_level_one = $service->createGroup($manager, 'collection_level_one', null, $project->collection, false);
        $collection_level_two = $service->createGroup($user, 'collection_level_two', null, $project->collection, false);
        $collection_level_three = $service->createGroup($user, 'collection_level_three', null, $collection_level_one, false);
        $collection_level_four = $service->createGroup($user, 'collection_level_four', null, $collection_level_three, false);

        $this->assertEquals(4, $project->collection->getDescendants()->count());

        // delete a collection close to the top
        $trashed = $service->permanentlyDeleteGroup($collection_level_one, $manager);

        // assert that target collection and all sub-collections are deleted

        $collection = $project->collection->fresh();

        $this->assertTrue($trashed);
        $this->assertEquals(1, $collection->getDescendants()->count());
    }

    public function test_partner_can_permanently_delete_collections_in_project()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $manager = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        });
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory('KBox\Project')->create([
            'user_id' => $manager->id
        ]);
        $project->users()->attach($user);
            
        //create a hierarchy
        $collection_level_one = $service->createGroup($user, 'collection_level_one', null, $project->collection, false);
        $collection_level_two = $service->createGroup($user, 'collection_level_two', null, $project->collection, false);
        $collection_level_three = $service->createGroup($user, 'collection_level_three', null, $collection_level_one, false);
        $collection_level_four = $service->createGroup($user, 'collection_level_four', null, $collection_level_three, false);

        $this->assertEquals(4, $project->collection->getDescendants()->count());

        
        $trashed = $service->permanentlyDeleteGroup($collection_level_one, $user);
        $this->assertTrue($trashed);
        $this->assertEquals(1, $project->collection->getDescendants()->count());
    }

    public function test_partner_cannot_permanently_delete_collections_in_project()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $manager = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        });
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory('KBox\Project')->create([
            'user_id' => $manager->id
        ]);
        $project->users()->attach($user);
            
        //create a hierarchy
        $collection_level_one = $service->createGroup($manager, 'collection_level_one', null, $project->collection, false);
        $collection_level_two = $service->createGroup($user, 'collection_level_two', null, $project->collection, false);
        $collection_level_three = $service->createGroup($user, 'collection_level_three', null, $collection_level_one, false);
        $collection_level_four = $service->createGroup($user, 'collection_level_four', null, $collection_level_three, false);

        $this->assertEquals(4, $project->collection->getDescendants()->count());

        try {
            $trashed = $service->permanentlyDeleteGroup($collection_level_one, $user);
            $this->fail('Expected forbidden exception');
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.delete.forbidden_delete_project_collection_not_manager', ['collection' => $collection_level_one->name]), $ex->getMessage());
        }
    }

    public function test_user_cannot_trash_my_personal_collection()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $creator = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);

        $this->assertEquals(4, $collection_root->getDescendants()->count());

        
        // delete a collection close to the top
        try {
            $service->deleteGroup($user, $collection_level_one);
            $this->fail('Expected exception, but trash continued');
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.delete.forbidden_trash_personal_collection', ['collection' => $collection_level_one->name]), $ex->getMessage());
        }
    }

    public function test_user_cannot_permanently_delete_my_trashed_personal_collection()
    {
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $creator = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory('KBox\User')->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);

        $service->deleteGroup($creator, $collection_level_one);

        $this->assertEquals(1, $collection_root->getDescendants()->count());

        try {
            $collection = Group::withTrashed()->findOrFail($collection_level_one->id);
            $service->permanentlyDeleteGroup($collection, $user);
            $this->fail('Expected exception, but delete continued');
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.delete.forbidden_trash_personal_collection', ['collection' => $collection_level_one->name]), $ex->getMessage());
        }
    }
}
