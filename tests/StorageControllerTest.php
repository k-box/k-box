<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Option;

class StorageControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetIndex()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createAdminUser();
        $this->actingAs($user);

        $this->visit(route('administration.storage.index'));
        $this->assertViewHas('reindex');
        $this->assertViewHas('status');
        $this->assertViewHas('disks');
    }
    
    public function testReindexWithEmptyStorage()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createAdminUser();
        $this->actingAs($user);

        $this->visit(route('administration.storage.index'));

        $this->assertViewHas('reindex');

        Option::put('dms.reindex.executing', true);
        Option::put('dms.reindex.pending', 0);
        Option::put('dms.reindex.completed', 0);
        Option::put('dms.reindex.total', 0);

        $this->visit(route('administration.storage.index'));

        $this->assertViewHas('reindex');

        $reindex = $this->response->original->reindex;

        $this->assertTrue(!!$reindex['executing']);
    }

}
