<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Option;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NetworkAdministrationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testNetworkPositiveConnection()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);

        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->never();
        $adapter->shouldReceive('canConnect')->andReturn(['status' => 'ok']);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)->get(route('administration.network.index'));

        $response->assertViewHas('local_connection', 'success');
        $response->assertViewHas('local_connection_bool', true);
        $response->assertViewHas('remote_connection', 'success');
        $response->assertViewHas('remote_connection_bool', true);
    }
    
    public function testNetworkNegativeConnection()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);

        $adapter = $this->withKlinkAdapterMock();

        $adapter->shouldReceive('isNetworkEnabled')->never();
        $adapter->shouldReceive('canConnect')->andReturn(['status' => 'error', 'error' => 'An error message']);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)->get(route('administration.network.index'));

        $response->assertViewHas('local_connection', 'failed');
        $response->assertViewHas('local_connection_bool', false);
        $response->assertViewHas('local_connection_error', 'An error message');

        $response->assertViewHas('remote_connection', 'failed');
        $response->assertViewHas('remote_connection_bool', false);
        $response->assertViewHas('remote_connection_error', 'An error message');
    }
}
