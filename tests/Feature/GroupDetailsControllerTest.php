<?php

namespace Tests\Feature;

use Tests\TestCase;

use KBox\Documents\Services\DocumentsService;
use KBox\Group;
use KBox\Project;
use KBox\Shared;
use KBox\User;

class GroupDetailsControllerTest extends TestCase
{
    public function test_details_for_personal_collection()
    {
        $user = User::factory()->partner()->create();

        $collection = Group::factory()->create([
            'user_id' => $user->getKey(),
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
        $user = User::factory()->partner()->create();

        $collection = Group::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('groups.detail', $collection->getKey()));

        $response->assertForbidden();
    }

    public function test_details_for_project_collection()
    {
        $service = app(DocumentsService::class);

        $project = Project::factory()->create();

        $user = User::factory()->partner()->create();

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
        $user = User::factory()->partner()->create();
        
        $shared_with = User::factory()->partner()->create();

        $collection = Group::factory()->create([
            'user_id' => $user->getKey(),
        ]);

        $share = Shared::factory()->create([
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
