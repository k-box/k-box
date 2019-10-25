<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\Documents\Services\DocumentsService;
use KBox\Group;
use KBox\Pagination\SearchResultsPaginator;
use KBox\Shared;
use KBox\User;

class SharedWithMePageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_shared_with_me_are_listed()
    {
        $this->withoutMiddleware();

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $collection_creator = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $service = app(DocumentsService::class);
        
        $single_root_collection = factory(Group::class)->create([
            'user_id' => $collection_creator->getKey(),
            'is_private' => true,
            'name' => 'root',
        ]);
        
        $root_collection = factory(Group::class)->create([
            'user_id' => $collection_creator->getKey(),
            'is_private' => true,
            'name' => 'root',
        ]);

        $single_sub_collection = $service->createGroup($collection_creator, 'under', null, $root_collection);
        
        $hierarchy_root_collection = factory(Group::class)->create([
            'user_id' => $collection_creator->getKey(),
            'is_private' => true,
            'name' => 'root',
        ]);

        $hierarchy_sub_collection = $service->createGroup($collection_creator, 'under', null, $hierarchy_root_collection);
        
        $first_share = factory(Shared::class)->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $single_root_collection->getKey(),
        ]);

        $single_root_collection->delete();
        
        $second_share = factory(Shared::class)->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $single_sub_collection->getKey(),
        ]);
        
        $third_share = factory(Shared::class)->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $hierarchy_root_collection->getKey(),
        ]);
        
        $fourth_share = factory(Shared::class)->create([
            'user_id' => $collection_creator->getKey(),
            'sharedwith_id' => $user->id,
            'shareable_type' => Group::class,
            'shareable_id' => $hierarchy_sub_collection->getKey(),
        ]);

        $response = $this->actingAs($user)->get(route('documents.sharedwithme'));

        $response->assertOk();
        $response->assertViewHas('shared_with_me');

        $found_shares = $response->getData('shared_with_me');

        $this->assertInstanceOf(SearchResultsPaginator::class, $found_shares);
        $this->assertEquals(3, $found_shares->total());
    }
}
