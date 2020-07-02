<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\Documents\Services\DocumentsService;
use KBox\Group;
use KBox\Project;
use KBox\Shared;
use KBox\User;

class GroupDetailsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_details_for_personal_collection()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true
        ]);

        $response = $this->actingAs($user)
            ->get(route('groups.detail', $collection->getKey()));

        $response->assertOk();
        $response->assertViewIs('groups.detail');
        $response->assertViewHas('group', $collection);
        $response->assertViewHas('is_personal', true);
        $response->assertViewHas('is_project', false);
        $response->assertViewHas('can_share', true);

        $response->assertSee($user->name);
    }

    public function test_details_forbidden_if_collection_not_mine()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $collection = factory(Group::class)->create([
            'is_private' => true
        ]);

        $response = $this->actingAs($user)
            ->get(route('groups.detail', $collection->getKey()));

        $response->assertForbidden();
    }

    public function test_details_for_project_collection()
    {
        $service = app(DocumentsService::class);

        $project = factory(Project::class)->create();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project->users()->attach($user);
        
        $collection = $service->createGroup($user, 'My collection', null, $project->collection, false);

        $response = $this->actingAs($user)
            ->get(route('groups.detail', $collection->getKey()));

        $response->assertOk();
        $response->assertViewIs('groups.detail');
        $response->assertViewHas('group', $collection);
        $response->assertViewHas('is_personal', false);
        $response->assertViewHas('is_project', true);
        $response->assertViewHas('can_share', true);

        $response->assertSee($user->name);
    }

    public function test_details_for_shared_collection()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $shared_with = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'shareable_id' => $collection->getKey(),
            'shareable_type' => get_class($collection),
            'sharedwith_id' => $shared_with->getKey(),
        ]);

        $response = $this->actingAs($shared_with)
            ->get(route('groups.detail', $collection->getKey()));

        $response->assertOk();
        $response->assertViewIs('groups.detail');
        $response->assertViewHas('group', $collection);
        $response->assertViewHas('has_share', true);
        $response->assertViewHas('share', $share);
        $response->assertViewHas('can_share', true);

        $response->assertSee($user->name);
        $response->assertSee(trans('documents.descriptor.shared'));
        $response->assertSee(trans('share.shared_on'));
    }
}
