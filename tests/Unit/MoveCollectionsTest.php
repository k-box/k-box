<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\Capability;
use KBox\Exceptions\ForbiddenException;
use KBox\Exceptions\CollectionMoveException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Project;
use KBox\Shared;
use KBox\User;

class MoveCollectionsTest extends TestCase
{
    use DatabaseTransactions;

    private $documentService = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentService = app('KBox\Documents\Services\DocumentsService');
    }

    public function test_move_from_project_to_personal_is_denied_if_user_not_member_of_project()
    {
        $project = factory(\KBox\Project::class)->create();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $collection = $this->documentService->createGroup($user, 'personal', null, $project->collection, false);
        $collection_container = $this->documentService->createGroup($user, 'personal-container', null);
        $collection_under = $this->documentService->createGroup($user, 'personal-sub-collection', null, $collection, false);
        $collection_under2 = $this->documentService->createGroup($user, 'personal-sub-sub-collection', null, $collection_under, false);
        
        try {
            $this->documentService->moveProjectCollectionToPersonal($user, $collection, $collection_container);
            $this->fail("The collection move must have been denied");
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.move.errors.no_access_to_collection'), $ex->getMessage());
        }
    }

    public function test_move_from_project_to_personal_is_permitted()
    {
        $project = factory(\KBox\Project::class)->create();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project->users()->attach($user);
        
        $collection = $this->documentService->createGroup($user, 'personal', null, $project->collection, false);
        $collection_container = $this->documentService->createGroup($user, 'personal-container', null);
        $collection_under = $this->documentService->createGroup($user, 'personal-sub-collection', null, $collection, false);
        $collection_under2 = $this->documentService->createGroup($user, 'personal-sub-sub-collection', null, $collection_under, false);

        $this->documentService->moveProjectCollectionToPersonal($user, $collection, $collection_container);

        $collection_under = $collection_under->fresh();

        $this->assertTrue($collection->is_private);
        $this->assertTrue($collection_under->is_private);
        $this->assertNotNull($collection->parent_id);
        $this->assertEquals(0, $project->collection->getDescendants()->count());

        $this->assertEquals([true, true, true], $collection_container->getDescendants()->pluck('is_private')->toArray());
    }

    public function test_move_from_project_to_personal_is_blocked_when_collections_are_created_by_different_users()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $project = tap(factory(\KBox\Project::class)->create(), function ($p) use ($user) {
            $p->users()->attach($user);
        });
        
        $collection = $this->documentService->createGroup($user, 'project-level-1', null, $project->collection, false);
        $collection_container = $this->documentService->createGroup($user, 'personal-container', null);
        $collection_under = $this->documentService->createGroup($project->manager, 'project-level-2', null, $collection, false);
        $collection_under2 = $this->documentService->createGroup($user, 'project-level-3', null, $collection_under, false);

        try {
            $this->documentService->moveProjectCollectionToPersonal($user, $collection, $collection_container);
            $this->fail("The collection move must have been denied");
        } catch (CollectionMoveException $ex) {
            $this->assertTrue($ex->collection()->is($collection), 'The collection that I want to move is not reported');
            $this->assertEquals(1, $ex->causes()->count());
            $this->assertTrue($ex->causes()->first()->is($collection_under), 'The collection that caused the move abort cannot be found in the error');
            $this->assertEquals(CollectionMoveException::REASON_NOT_ALL_SAME_USER, $ex->reason());
            $this->assertEquals(trans('groups.move.errors.personal_not_all_same_user', [
                'collection' => $collection->name,
                'collection_cause' => $collection_under->name,
            ]), $ex->getMessage());
        }
    }

    public function test_move_from_personal_to_project()
    {
        $project = factory(\KBox\Project::class)->create();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $project->users()->attach($user);
        
        $collection = $this->documentService->createGroup($user, 'personal');
        $collection_under = $this->documentService->createGroup($user, 'personal-sub-collection', null, $collection);
        $collection_under2 = $this->documentService->createGroup($user, 'personal-sub-sub-collection', null, $collection_under);

        // move $collection under $project->collection()

        $this->documentService->movePersonalCollectionToProject($user, $collection, $project->collection);

        $collection_under = $collection_under->fresh();

        $this->assertFalse($collection->is_private);
        $this->assertFalse($collection_under->is_private);
        $this->assertNotNull($collection->parent_id);

        $this->assertEquals([false, false, false], $project->collection->getDescendants()->pluck('is_private')->toArray());
    }

    public function test_move_from_personal_to_project_is_denied_if_user_do_not_have_access_to_project()
    {
        $project = factory(\KBox\Project::class)->create();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $collection = $this->documentService->createGroup($user, 'personal');
        $collection_under = $this->documentService->createGroup($user, 'personal-sub-collection', null, $collection);
        $collection_under2 = $this->documentService->createGroup($user, 'personal-sub-sub-collection', null, $collection_under);

        // move $collection under $project->collection()

        try {
            $this->documentService->movePersonalCollectionToProject($user, $collection, $project->collection);
            $this->fail("The collection move must have been denied");
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.move.errors.no_access_to_collection'), $ex->getMessage());
        }
    }

    public function test_move_from_personal_to_project_denied_if_collection_has_shares_to_non_members()
    {
        $project = factory(Project::class)->create();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $project->users()->attach($user);
        
        $collection = $this->documentService->createGroup($user, 'personal');

        $share = factory(Shared::class)->create([
            'shareable_id' => $collection->getKey(),
            'shareable_type' => get_class($collection),
        ]);

        // move $collection under $project->collection()

        try {
            $this->documentService->movePersonalCollectionToProject($user, $collection, $project->collection);
            $this->fail("The collection move must have been denied");
        } catch (ForbiddenException $ex) {
            $this->assertEquals(trans('groups.move.errors.has_shares_to_non_members'), $ex->getMessage());
        }
    }
}
