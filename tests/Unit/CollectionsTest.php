<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Group;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\Exceptions\ForbiddenException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_trashing_personal_collections_trash_also_descendants()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
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
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $manager = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(\KBox\Project::class)->create([
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
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $manager = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(\KBox\Project::class)->create([
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
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $manager = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(\KBox\Project::class)->create([
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
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $manager = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(\KBox\Project::class)->create([
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
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
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
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
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

    public function test_user_cannot_trash_shared_collection()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        
        $collection = $service->createGroup($creator, 'collection_level_one', null, null);

        $share = $collection->shares()->create([
            'user_id' => $creator->id,
            'sharedwith_id' => $user->id,
            'sharedwith_type' => get_class($user),
            'token' => hash('sha256', 'token_content'),
        ]);
        
        try {
            $service->deleteGroup($user, $collection);
            $this->fail('Expected exception, but trash continued');
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.delete.forbidden_delete_shared_collection', ['collection' => $collection->name]), $ex->getMessage());
        }
    }

    public function test_create_collection_with_same_name_of_trashed_one()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);

        $service->deleteGroup($creator, $collection_level_one);

        $the_new_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);

        $this->assertNotNull($the_new_one, "Group with same name not created");
        $this->assertNotNull($collection_level_one->fresh()->deleted_at, "Group is not in the trash");
    }

    public function test_trash_collection_with_same_name_of_trashed_one()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);

        // let's trash it
        $service->deleteGroup($creator, $collection_level_one);

        // create a collection with same name under same parent
        $the_new_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $the_new_three = $service->createGroup($creator, 'collection_level_three', null, $the_new_one);

        // trash it
        $service->deleteGroup($creator, $the_new_one);

        $this->assertNotNull($the_new_one->fresh());
        $this->assertNull($collection_level_one->fresh());
        $this->assertNotNull($the_new_three->fresh());
        $this->assertNull($collection_level_three->fresh());
    }

    public function test_trash_collection_with_same_name_of_trashed_one_respect_users()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);

        // let's trash it
        $service->deleteGroup($creator, $collection_level_one);

        // create a collection with same name under same parent
        $the_new_one = $service->createGroup($user, 'collection_level_one', null, $collection_root);
        $the_new_three = $service->createGroup($user, 'collection_level_three', null, $the_new_one);

        // trash it
        $service->deleteGroup($user, $the_new_one);

        $this->assertNotNull($the_new_one->fresh(), "New collection is gone");
        $this->assertTrue($the_new_one->fresh()->trashed(), "New collection is not trashed");
        $this->assertTrue($the_new_three->fresh()->trashed(), "New collection is not trashed");
        $this->assertNull($collection_level_one->fresh());
        $this->assertNull($collection_level_three->fresh());
    }

    public function test_merge_collection()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);
        $collection_level_five = $service->createGroup($creator, 'collection_level_five', null, $collection_level_one);
        $collection_level_six = $service->createGroup($creator, 'collection_level_six', null, $collection_level_two);

        $merged = $collection_level_two->merge($collection_level_one);

        $this->assertEquals(0, $collection_level_one->fresh()->getDescendants()->count());
        $this->assertEquals(4, $merged->getDescendants()->count());
        $this->assertContains($collection_level_three->id, $merged->getChildren()->pluck('id')->toArray());
        $this->assertContains($collection_level_five->id, $merged->getChildren()->pluck('id')->toArray());
        $this->assertContains($collection_level_six->id, $merged->getChildren()->pluck('id')->toArray());
    }

    public function test_merge_with_trashed_collection()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_level_one', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);
        $collection_level_five = $service->createGroup($creator, 'collection_level_five', null, $collection_level_one);
        $collection_level_six = $service->createGroup($creator, 'collection_level_six', null, $collection_level_two);

        $service->deleteGroup($creator, $collection_level_one);

        $merged = $collection_level_two->merge($collection_level_one);

        $this->assertEquals(0, $collection_level_one->fresh()->getDescendants()->count());
        $this->assertEquals(1, $merged->getDescendants()->count());
        $this->assertEquals($collection_level_two->id, $collection_level_three->fresh()->parent_id);
        $this->assertContains($collection_level_three->id, $merged->getTrashedChildren()->pluck('id')->toArray());
        $this->assertContains($collection_level_five->id, $merged->getTrashedChildren()->pluck('id')->toArray());
        $this->assertContains($collection_level_six->id, $merged->getChildren()->pluck('id')->toArray());
    }

    public function test_trashed_collection_can_be_restored()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
        $user = tap(factory(\KBox\User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });
            
        //create a hierarchy
        $collection_root = $service->createGroup($creator, 'collection_root', null, null);
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $collection_root);
        $collection_level_three = $service->createGroup($creator, 'collection_level_three', null, $collection_level_one);
        $collection_level_four = $service->createGroup($creator, 'collection_level_four', null, $collection_level_three);

        // let's trash it
        $service->deleteGroup($creator, $collection_level_one);

        // create a collection with same name under same parent
        $the_new_one = $service->createGroup($creator, 'collection_level_one', null, $collection_root);

        // restore trashed collection
        $restoredCollection = $collection_level_one->restoreFromTrash();

        $the_new_one = $the_new_one->fresh();

        $this->assertNotNull($the_new_one, "New collection is gone");
        $this->assertNull($collection_level_one->fresh(), "Old trashed collection was expected to be permanently removed");
        $this->assertTrue($the_new_one->is_private);
        $this->assertEquals($restoredCollection->id, $the_new_one->id);

        $restoredDescendants = $restoredCollection->getDescendants()->pluck('id')->toArray();

        $this->assertContains($collection_level_three->id, $restoredDescendants);
        $this->assertContains($collection_level_four->id, $restoredDescendants);
        
        $this->assertTrue($collection_level_three->fresh()->is_private);
        $this->assertTrue($collection_level_four->fresh()->is_private);
    }

    public function test_trashed_project_collection_can_be_restored()
    {
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $creator = tap(factory(User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user = tap(factory(User::class)->create(), function ($user) {
            $user->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(Project::class)->create(['user_id' => $creator->id]);

        //create a hierarchy
        $collection_level_one = $service->createGroup($creator, 'collection_level_one', null, $project->collection, false);
        $collection_level_two = $service->createGroup($creator, 'collection_level_two', null, $project->collection, false);
        $collection_level_three = $service->createGroup($user, 'collection_level_three', null, $collection_level_one, false);
        $collection_level_four = $service->createGroup($user, 'collection_level_four', null, $collection_level_three, false);

        // let's trash it
        $service->deleteGroup($creator, $collection_level_one);

        // create a collection with same name under same parent
        $the_new_one = $service->createGroup($user, 'collection_level_one', null, $project->collection, false);

        // restore trashed collection
        $restoredCollection = $collection_level_one->restoreFromTrash();

        $the_new_one = $the_new_one->fresh();

        $this->assertNotNull($the_new_one, "New collection is gone");
        $this->assertNull($collection_level_one->fresh(), "Old trashed collection was expected to be permanently removed");
        $this->assertFalse($the_new_one->is_private);
        $this->assertEquals($restoredCollection->id, $the_new_one->id);

        $restoredDescendants = $restoredCollection->getDescendants()->pluck('id')->toArray();

        $this->assertContains($collection_level_three->id, $restoredDescendants);
        $this->assertContains($collection_level_four->id, $restoredDescendants);
        
        $this->assertFalse($collection_level_three->fresh()->is_private);
        $this->assertFalse($collection_level_four->fresh()->is_private);
    }
}
