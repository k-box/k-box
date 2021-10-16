<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Group;
use KBox\Shared;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;

class FindSharingTargetsControllerTest extends TestCase
{
    public function test_target_search_requires_two_characters()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();
        
        $user_target = User::factory()->partner()->create();

        $other_target = User::factory()->partner()->create();

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'a',
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            's'
        ]);
    }

    public function test_share_users_autocomplete_without_current_selection()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();
        
        $user_target = User::factory()->partner()->create();

        $other_target = User::factory()->partner()->create();

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => $user_target->email,
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                [
                    'id' => $user_target->id,
                    'name' => $user_target->name,
                    'avatar' => $user_target->avatar,
                ]
            ]
        ]);
    }

    public function test_share_users_autocomplete_with_email_address()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        
        $user_target = User::factory()->partner()->create();

        $other_target = User::factory()->partner()->create();

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => $user_target->email,
            'documents' => [$document->id],
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                [
                    'id' => $user_target->id,
                    'name' => $user_target->name,
                    'avatar' => $user_target->avatar,
                ]
            ]
        ]);
    }

    public function test_share_users_autocomplete_with_name()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        
        $user_target = tap(User::factory()->create([
            'name' => 'juliet o\'hara'
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $other_target = User::factory()->partner()->create();

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'j o\'h',
            'documents' => [$document->id],
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                [
                    'id' => $user_target->id,
                    'name' => $user_target->name,
                    'avatar' => $user_target->avatar,
                ]
            ]
        ]);
    }
    public function test_sql_is_properly_escaped()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        
        $user_target = tap(User::factory()->create([
            'name' => 'juliet'
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $other_target = User::factory()->partner()->create();

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => "jul'; DROP TABLE users;",
            'documents' => [$document->id],
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => []
        ]);
    }

    public function test_terms_are_normalized()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        
        $user_target = tap(User::factory()->create([
            'name' => 'juliet'
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $other_target = User::factory()->partner()->create();

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => urlencode('jul%iet'),
            'documents' => [$document->id],
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                [
                    'id' => $user_target->id,
                    'name' => $user_target->name,
                    'avatar' => $user_target->avatar,
                ]
            ]
        ]);
    }

    public function test_targets_are_returned_in_the_correct_order()
    {
        $user = User::factory()->partner()->create();

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'spencer o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'henry o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'barton o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'carlton o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'john o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'james o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'james nolan']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'john nolan']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'jane nolan']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
        ]);

        $expected_data = $targets->splice(0, 6)->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar ?? null,
            ];
        })->reverse();
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => $expected_data->toArray()
        ]);
    }

    public function test_already_selected_users_can_be_excluded()
    {
        $user = User::factory()->partner()->create();

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'spencer o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'henry o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'barton o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'carlton o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'john o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'james o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
            'e' => [$targets->last()->id]
        ]);

        $expected_data = $targets->take(6)->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar ?? null,
            ];
        })->reverse();
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => $expected_data->toArray()
        ]);
    }

    public function test_already_existing_targets_are_excluded_for_documents()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'spencer o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'henry o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'sharedwith_id' => $targets->first()->getKey(),
            'shareable_id' => $document->getKey(),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
            'documents' => [$document->getKey()],
        ]);

        $expected_data = $targets->splice(1, 2)->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar ?? null,
            ];
        })->reverse();
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => $expected_data->toArray()
        ]);
    }

    public function test_already_existing_targets_are_excluded_for_collections()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            // 'is_private' => true
        ]);

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'spencer o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'henry o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'sharedwith_id' => $targets->first()->getKey(),
            'shareable_id' => $collection->getKey(),
            'shareable_type' => get_class($collection),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
            'collections' => [$collection->getKey()],
        ]);

        $expected_data = $targets->splice(1, 2)->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar ?? null,
            ];
        })->reverse();
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => $expected_data->toArray()
        ]);
    }

    public function test_project_collections_autocomplete_not_possible()
    {
        // This is to prevent the possibility to select users to share with
        // in case of a single selection on a project collection as
        // per https://github.com/k-box/k-box/pull/355#issuecomment-551448888
        // and https://github.com/k-box/k-box/issues/356

        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $collection = factory(Group::class)->state('project')->create([
            'user_id' => $user->getKey(),
        ]);

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
            'collections' => [$collection->getKey()],
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => []
        ]);
    }

    public function test_multiple_selection_handled_as_no_selection()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            // 'is_private' => true
        ]);

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'spencer o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'henry o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'sharedwith_id' => $targets->first()->getKey(),
            'shareable_id' => $collection->getKey(),
            'shareable_type' => get_class($collection),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
            'collections' => [$collection->getKey()],
            'documents' => [$document->getKey()],
        ]);

        $expected_data = $targets->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar ?? null,
            ];
        })->reverse();
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => $expected_data->toArray()
        ]);
    }

    public function test_multiple_selection_with_project_collection_return_no_results()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $collection = factory(Group::class)->state('project')->create([
            'user_id' => $user->getKey(),
        ]);

        $targets = collect([
            tap(User::factory()->create(['name' => 'juliet o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'spencer o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
            tap(User::factory()->create(['name' => 'henry o\'hara']), function ($u) {
                $u->addCapabilities(Capability::$PARTNER);
            }),
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'sharedwith_id' => $targets->first()->getKey(),
            'shareable_id' => $collection->getKey(),
            'shareable_type' => get_class($collection),
        ]);

        $response = $this->actingAs($user)->json('POST', route('shares.targets.find'), [
            's' => 'o\'h',
            'collections' => [$collection->getKey()],
            'documents' => [$document->getKey()],
        ]);
        
        $response->assertOk();
        $response->assertExactJson([
            'data' => []
        ]);
    }
}
