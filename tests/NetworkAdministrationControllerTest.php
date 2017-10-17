<?php

use Tests\BrowserKitTestCase;
use KlinkDMS\Option;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NetworkAdministrationControllerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function testNetworkPositiveConnection()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);

        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->never();
        $adapter->shouldReceive('canConnect')->andReturn(['status' => 'ok']);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.network.index'));

        $this->assertViewHas('local_connection', 'success');
        $this->assertViewHas('local_connection_bool', true);
        $this->assertViewHas('remote_connection', 'success');
        $this->assertViewHas('remote_connection_bool', true);
    }
    
    public function testNetworkNegativeConnection()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);

        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->never();
        $adapter->shouldReceive('canConnect')->andReturn(['status' => 'error', 'error' => 'An error message']);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.network.index'));

        $this->assertViewHas('local_connection', 'failed');
        $this->assertViewHas('local_connection_bool', false);
        $this->assertViewHas('local_connection_error', 'An error message');

        $this->assertViewHas('remote_connection', 'failed');
        $this->assertViewHas('remote_connection_bool', false);
        $this->assertViewHas('remote_connection_error', 'An error message');
    }
}
