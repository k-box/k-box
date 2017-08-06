<?php

use Tests\BrowserKitTestCase;

class NetworkAdministrationControllerTest extends BrowserKitTestCase
{
    public function testNetworkPositiveConnection()
    {
        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->andReturn(true);
        $adapter->shouldReceive('test')->andReturn(['result' => true, 'error' => null]);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.network.index'));

        $this->assertViewHas('klink_network_connection', 'success');
        $this->assertViewHas('klink_network_connection_bool', true);
    }
    
    public function testNetworkNegativeConnection()
    {
        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->andReturn(false);
        $adapter->shouldReceive('test')->andReturn(['result' => false, 'error' => null]);

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.network.index'));

        $this->assertViewHas('klink_network_connection', 'failed');
        $this->assertViewHas('klink_network_connection_bool', false);
        $this->assertViewHas('klink_network_connection_error', null);
    }
}
