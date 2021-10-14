<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Option;
use Tests\TestCase;
use KBox\Capability;

class StorageControllerTest extends TestCase
{
    public function test_storage_administration_page_can_be_viewed()
    {
        $this->withKlinkAdapterFake();

        $this->withExceptionHandling();

        Option::put('dms.reindex.executing', false);
        Option::put('dms.reindex.pending', 0);
        Option::put('dms.reindex.completed', 0);
        Option::put('dms.reindex.total', 0);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $response = $this->actingAs($user)->get(route('administration.storage.index'));

        $response->assertViewHas('reindex');
        $response->assertViewHas('storage');
    }
    
    public function test_reindex_with_empty_storage()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)->get(route('administration.storage.index'));

        $response->assertViewHas('reindex');

        Option::put('dms.reindex.executing', true);
        Option::put('dms.reindex.pending', 0);
        Option::put('dms.reindex.completed', 0);
        Option::put('dms.reindex.total', 0);

        $response = $this->actingAs($user)->get(route('administration.storage.index'));

        $response->assertViewHas('reindex');

        $reindex_data = $response->data('reindex');

        $this->assertTrue(! ! $reindex_data['executing']);
    }
}
