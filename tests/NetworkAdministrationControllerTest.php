<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NetworkAdministrationControllerTest extends TestCase
{
    
    public function testNetworkPositiveConnection()
    {
        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->andReturn(false);
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
