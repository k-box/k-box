<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectionControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_move_from_project_to_personal_is_permitted()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = factory(\KBox\Project::class)->create();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $project->users()->attach($user);
        
        $collection = $service->createGroup($user, 'personal', null, $project->collection, false);
        $collection_container = $service->createGroup($user, 'personal-container', null);
        $collection_under = $service->createGroup($user, 'personal-sub-collection', null, $collection, false);
        $collection_under2 = $service->createGroup($user, 'personal-sub-sub-collection', null, $collection_under, false);
        
        $response = $this->actingAs($user)->json('PUT', '/documents/groups/'.$collection->id, [
            'private' => true,
            'parent' => $collection_container->id,
            'dry_run' => 0,
            'action' => 'move',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $collection->id
        ]);
    }

    public function test_move_from_project_to_personal_is_blocked_when_collections_are_created_by_different_users()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $project = tap(factory(\KBox\Project::class)->create(), function ($p) use ($user) {
            $p->users()->attach($user);
        });
        
        $collection = $service->createGroup($user, 'project-level-1', null, $project->collection, false);
        $collection_container = $service->createGroup($user, 'personal-container', null);
        $collection_under = $service->createGroup($project->manager, 'project-level-2', null, $collection, false);
        $collection_under2 = $service->createGroup($user, 'project-level-3', null, $collection_under, false);

        $response = $this->actingAs($user)->json('PUT', '/documents/groups/'.$collection->id, [
            'private' => true,
            'parent' => $collection_container->id,
            'dry_run' => 0,
            'action' => 'move',
        ]);

        $response->assertStatus(409);

        $response->assertJson([
            'error' => trans('groups.move.errors.personal_not_all_same_user', [
                'collection' => $collection->name,
                'collection_cause' => $collection_under->name,
            ])
        ]);
    }

    public function test_move_from_personal_to_project_is_permitted()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = factory(\KBox\Project::class)->create();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $project->users()->attach($user);
        
        $collection = $service->createGroup($user, 'personal');
        $collection_under = $service->createGroup($user, 'personal-sub-collection', null, $collection);
        $collection_under2 = $service->createGroup($user, 'personal-sub-sub-collection', null, $collection_under);
        
        $response = $this->actingAs($user)->json('PUT', '/documents/groups/'.$collection->id, [
            'private' => false,
            'parent' => $project->collection->id,
            'dry_run' => 0,
            'action' => 'move',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $collection->id
        ]);
    }

    public function test_move_from_personal_to_personal()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $collection = $service->createGroup($user, 'personal');
        $collection_under = $service->createGroup($user, 'personal-sub-collection', null, $collection);
        $collection_under2 = $service->createGroup($user, 'personal-sub-sub-collection', null, $collection_under);
        
        $response = $this->actingAs($user)->json('PUT', '/documents/groups/'.$collection_under2->id, [
            'private' => false,
            'parent' => $collection->id,
            'dry_run' => 0,
            'action' => 'move',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $collection_under2->id,
            'parent_id' => $collection->id
        ]);
    }
    
    public function test_move_from_project_to_project()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $project = factory(\KBox\Project::class)->create();
        
        $collection = $project->collection;
        $collection_under = $service->createGroup($user, 'personal-sub-collection', null, $collection, false);
        $collection_under2 = $service->createGroup($user, 'personal-sub-sub-collection', null, $collection_under, false);
        
        $response = $this->actingAs($user)->json('PUT', '/documents/groups/'.$collection_under2->id, [
            'private' => false,
            'parent' => $collection->id,
            'dry_run' => 0,
            'action' => 'move',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $collection_under2->id,
            'parent_id' => $collection->id
        ]);
    }
    
    public function test_move_from_personal_to_project_cannot_be_perfomed_with_move_action()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $project = factory(\KBox\Project::class)->create();
        
        $collection = $service->createGroup($user, 'personal');
        $collection_under = $service->createGroup($user, 'personal-sub-collection', null, $collection);
        $collection_under2 = $service->createGroup($user, 'personal-sub-sub-collection', null, $collection_under);
        
        $response = $this->actingAs($user)->json('PUT', '/documents/groups/'.$collection_under2->id, [
            'parent' => $project->collection->id,
            'dry_run' => 0,
            'action' => 'move',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $collection_under2->id,
            'parent_id' => $collection_under->id
        ]);
    }
}
