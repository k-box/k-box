<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Group;
use KBox\Shared;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Events\ShareCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use KBox\Notifications\ShareCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Project;

class SharingControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function document_share_links()
    {
        return [
            [Capability::$ADMIN, 'documents.sharedwithme'],
            [Capability::$PROJECT_MANAGER, 'documents.sharedwithme'],
            [Capability::$PARTNER, 'documents.sharedwithme'],
            [[Capability::RECEIVE_AND_SEE_SHARE], 'shares.index'],
        ];
    }

    public function collection_share_links()
    {
        return [
            [Capability::$ADMIN, 'documents.groups.show'],
            [Capability::$PROJECT_MANAGER, 'documents.groups.show'],
            [Capability::$PARTNER, 'documents.groups.show'],
            [[Capability::RECEIVE_AND_SEE_SHARE], 'shares.group'],
        ];
    }

    public function test_share_created()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        // using a partner user, create a document and share it with a second user
        // at the end the ShareCreated event must be raised

        Event::fake([
            ShareCreated::class
        ]);

        $data = [
            'users' => [$user_target->id],
            'documents' => [$document->id],
        ];

        $response = $this->actingAs($user)
                         ->json('POST', route('shares.store'), $data);

        $response
            ->assertStatus(201)
            ->assertJson(['status' => 'ok']);

        Event::assertDispatched(ShareCreated::class, function ($e) use ($user, $user_target, $document) {
            return $e->share->shareable->is($document)
                && $e->share->user->is($user)
                && $e->share->sharedwith->is($user_target);
        });
    }

    public function test_share_created_notification_sent()
    {
        $this->withKlinkAdapterFake();

        Notification::fake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $data = [
            'users' => [$user_target->id],
            'documents' => [$document->id],
        ];

        $response = $this->actingAs($user)
                         ->json('POST', route('shares.store'), $data);

        $response
            ->assertStatus(201)
            ->assertJson(['status' => 'ok']);
        
        Notification::assertSentTo(
            $user_target,
            ShareCreatedNotification::class
        );
    }

    /**
     * @dataProvider document_share_links
     */
    public function test_links_for_document_shares($target_capabilities, $expected_route_name)
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) use ($target_capabilities) {
            $u->addCapabilities($target_capabilities);
        });

        $to_be_shared = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'sharedwith_id' => $user_target->getKey(),
            'shareable_id' => $to_be_shared->getKey(),
        ]);

        $url = route('shares.show', $share->token);

        $params = [
            'highlight' => $to_be_shared->id
        ];

        $response = $this->actingAs($user_target)->get($url);
            
        $response->assertRedirect(route($expected_route_name, $params));
    }

    /**
     * @dataProvider collection_share_links
     */
    public function test_links_for_collection_shares($target_capabilities, $expected_route_name)
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) use ($target_capabilities) {
            $u->addCapabilities($target_capabilities);
        });

        $to_be_shared = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true,
        ]);

        $share = factory(Shared::class)->create([
            'user_id' => $user->getKey(),
            'sharedwith_id' => $user_target->getKey(),
            'shareable_id' => $to_be_shared->getKey(),
            'shareable_type' => get_class($to_be_shared),
        ]);

        $url = route('shares.show', $share->token);

        $response = $this->actingAs($user_target)->get($url);
            
        $response->assertRedirect(route($expected_route_name, $to_be_shared->id));
    }
    
    public function test_project_collections_cannot_be_shared()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $to_be_shared = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => false,
        ]);

        $data = [
            'users' => [$user_target->id],
            'groups' => [$to_be_shared->id],
        ];

        $response = $this->actingAs($user)
                         ->json('POST', route('shares.store'), $data);

        $response
            ->assertStatus(422)
            ->assertJsonStructure(['groups']);
    }

    public function test_sharing_twice_is_not_permitted()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        Event::fake([
            ShareCreated::class
        ]);

        $this->actingAs($user);

        $data = [
            'users' => [$user_target->id],
            'documents' => [$document->id],
        ];

        $this->json('POST', route('shares.store'), $data);
        
        $this->json('POST', route('shares.store'), $data);

        Event::assertDispatched(ShareCreated::class, 1);
    }

    public function test_unshare()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $share = factory(Shared::class)->create([
            'user_id' => $user->id,
            'sharedwith_id' => $user_target->id,
        ]);
            
        $response =  $this->actingAs($user)
            ->json('DELETE', route('shares.destroy', $share->id), []);

        $response->assertJson(['status' => 'ok', 'message' => trans('share.removed')]);
    }

    public function test_share_dialog_with_single_document()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $response = $this->actingAs($user)->get(route('shares.create', [
            'collections' => [],
            'documents' => [$document->id]
        ]));
        
        $response->assertOk();
        $response->assertSee(__('Enable public link'));
        $response->assertViewHas('is_network_enabled', false);
        $response->assertViewHas('can_make_public', false);
        $response->assertViewHas('has_documents', true);
        $response->assertViewHas('has_groups', false);
        $response->assertViewHas('elements_count', 1);
        $response->assertViewHas('is_multiple_selection', false);
        $response->assertViewHas('is_public', false);
        $response->assertViewHas('is_collection', false);
        $response->assertViewHas('can_add_users', true);
        $response->assertViewMissing('users');

        $this->assertEmpty($response->getData('existing_shares'));
        $this->assertEmpty($response->getData('groups'));

        $this->assertEquals($document->id, $response->getData('documents')->first()->id);
    }

    public function test_share_dialog_with_multiple_documents()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document1 = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        $document2 = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $response = $this->actingAs($user)->get(route('shares.create', [
            'collections' => [],
            'documents' => [$document1->id, $document2->id]
        ]));

        $response->assertOk();
        $response->assertViewHas('is_network_enabled', false);
        $response->assertViewHas('can_make_public', false);
        $response->assertViewHas('has_documents', true);
        $response->assertViewHas('has_groups', false);
        $response->assertViewHas('elements_count', 2);
        $response->assertViewHas('is_multiple_selection', true);
        $response->assertViewHas('is_public', false);
        $response->assertViewHas('is_collection', false);
        $response->assertViewHas('can_add_users', true);
        $response->assertViewMissing('users');

        $this->assertEmpty($response->getData('existing_shares'));
        $this->assertEmpty($response->getData('groups'));

        $this->assertEquals([$document1->id, $document2->id], $response->getData('documents')->pluck('id')->toArray());
    }

    public function test_share_dialog_with_single_collection()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true,
        ]);

        $response = $this->actingAs($user)->get(route('shares.create', [
            'collections' => [$collection->id],
            'documents' => [],
        ]));

        $response->assertOk();
        $response->assertViewHas('is_network_enabled', false);
        $response->assertViewHas('can_make_public', false);
        $response->assertViewHas('has_documents', false);
        $response->assertViewHas('has_groups', true);
        $response->assertViewHas('elements_count', 1);
        $response->assertViewHas('is_multiple_selection', false);
        $response->assertViewHas('is_public', false);
        $response->assertViewHas('is_collection', true);
        $response->assertViewHas('can_add_users', true);
        $response->assertViewMissing('users');

        $this->assertEmpty($response->getData('existing_shares'));

        $this->assertEquals([$collection->id], $response->getData('groups')->pluck('id')->toArray());
    }

    public function test_share_dialog_with_single_project_collection()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project = factory(Project::class)->create([
            'user_id' => $user_target->getKey(),
        ]);

        $collection = $project->collection;

        $response = $this->actingAs($user)->get(route('shares.create', [
            'collections' => [$collection->id],
            'documents' => [],
        ]));

        $response->assertOk();
        $response->assertViewHas('is_network_enabled', false);
        $response->assertViewHas('can_make_public', false);
        $response->assertViewHas('has_documents', false);
        $response->assertViewHas('has_groups', true);
        $response->assertViewHas('elements_count', 1);
        $response->assertViewHas('is_multiple_selection', false);
        $response->assertViewHas('is_public', false);
        $response->assertViewHas('is_collection', true);
        $response->assertViewHas('can_add_users', false);
        $response->assertViewHas('project', null);
        $response->assertViewHas('can_edit_project', false);
        $response->assertViewMissing('users');

        $this->assertEmpty($response->getData('existing_shares'));

        $this->assertEquals([$collection->id], $response->getData('groups')->pluck('id')->toArray());
    }

    public function test_share_dialog_with_documents_and_collections()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document1 = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        $document2 = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $collection1 = $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true,
        ]);
        $collection2 = $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true,
        ]);

        $response = $this->actingAs($user)->get(route('shares.create', [
            'collections' => [$collection1->id, $collection2->id],
            'documents' => [$document1->id, $document2->id]
        ]));

        $response->assertOk();
        $response->assertViewHas('is_network_enabled', false);
        $response->assertViewHas('can_make_public', false);
        $response->assertViewHas('has_documents', true);
        $response->assertViewHas('has_groups', true);
        $response->assertViewHas('elements_count', 4);
        $response->assertViewHas('is_multiple_selection', true);
        $response->assertViewHas('is_public', false);
        $response->assertViewHas('is_collection', false);
        $response->assertViewHas('can_add_users', true);
        $response->assertViewHas('project', null);
        $response->assertViewHas('can_edit_project', false);
        $response->assertViewMissing('users');

        $this->assertEmpty($response->getData('existing_shares'));

        $this->assertEquals([$document1->id, $document2->id], $response->getData('documents')->pluck('id')->toArray());
        $this->assertEquals([$collection1->id, $collection2->id], $response->getData('groups')->pluck('id')->toArray());
    }

    public function test_share_dialog_with_documents_and_project_collections()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $user_target = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document1 = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);
        $document2 = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->getKey()
        ]);

        $collection1 = $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => true,
        ]);
        $collection2 = $collection = factory(Group::class)->create([
            'user_id' => $user->getKey(),
            'is_private' => false,
        ]);

        $response = $this->actingAs($user)->get(route('shares.create', [
            'collections' => [$collection1->id, $collection2->id],
            'documents' => [$document1->id, $document2->id]
        ]));

        $response->assertOk();
        $response->assertViewHas('is_network_enabled', false);
        $response->assertViewHas('can_make_public', false);
        $response->assertViewHas('has_documents', true);
        $response->assertViewHas('has_groups', true);
        $response->assertViewHas('elements_count', 4);
        $response->assertViewHas('is_multiple_selection', true);
        $response->assertViewHas('is_public', false);
        $response->assertViewHas('is_collection', false);
        $response->assertViewHas('can_add_users', false);
        $response->assertViewHas('project', null);
        $response->assertViewHas('can_edit_project', false);
        $response->assertViewMissing('users');

        $this->assertEmpty($response->getData('existing_shares'));

        $this->assertEquals([$document1->id, $document2->id], $response->getData('documents')->pluck('id')->toArray());
        $this->assertEquals([$collection1->id, $collection2->id], $response->getData('groups')->pluck('id')->toArray());
    }
}
