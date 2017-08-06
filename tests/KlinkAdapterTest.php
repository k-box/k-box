<?php

use Tests\BrowserKitTestCase;

class KlinkAdapterTest extends BrowserKitTestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testAdapterInstantiation()
    {
        $adapter = app('klinkadapter');

        $this->assertInstanceOf(Klink\DmsAdapter\Contracts\KlinkAdapter::class, $adapter);
        $this->assertInstanceOf(Klink\DmsAdapter\KlinkAdapter::class, $adapter);
    }

    /**
     * Test if a mock of Klink\DmsAdapter\Contracts\KlinkAdapter is properly returned.
     *
     * A mock of Klink\DmsAdapter\Contracts\KlinkAdapter is added to the Service Container
     * and then tested if is returned when asking for an implementation of the
     * Klink\DmsAdapter\Contracts\KlinkAdapter contract
     */
    public function testSwapInstance()
    {
        $adapterFromContract = app('Klink\DmsAdapter\Contracts\KlinkAdapter');

        // Swap in the service container the instance returned when asking for the
        // implementation of the KlinkAdapter contract
        $this->swap(
            'Klink\DmsAdapter\Contracts\KlinkAdapter',
            Mockery::mock(Klink\DmsAdapter\Contracts\KlinkAdapter::class)
        );

        $adapterFromMock = app('Klink\DmsAdapter\Contracts\KlinkAdapter');

        $this->assertNotEquals(get_class($adapterFromContract), get_class($adapterFromMock));
    }
    
    /**
     * Tests that the trai UseFakeAdapter added to the BrowserKitTestCase is usable
     */
    public function testUseMockAdapter()
    {
        $mock = $this->withKlinkAdapterMock();

        $adapter = app('Klink\DmsAdapter\Contracts\KlinkAdapter');

        $this->assertInstanceOf('Mockery\MockInterface', $mock);
        $this->assertInstanceOf('Klink\DmsAdapter\Contracts\KlinkAdapter', $adapter);
        $this->assertEquals(get_class($mock), get_class($adapter));
    }
}
