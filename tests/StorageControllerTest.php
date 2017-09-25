<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\Option;

class StorageControllerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
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
        $this->assertViewHas('storage');
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

        $this->assertTrue(! ! $reindex['executing']);
    }
}
