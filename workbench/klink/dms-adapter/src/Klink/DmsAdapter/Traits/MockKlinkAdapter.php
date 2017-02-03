<?php namespace Klink\DmsAdapter\Traits;

use Mockery;
use RuntimeException;
use Mockery\MockInterface;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;

/**
 * Add support for mocking a class
 *
 * When added to a class, adds the ability to mock all its methods
 *
 * @uses SwapInstance
 */
trait MockKlinkAdapter
{

    use SwapInstance;

    /**
     * Hotswap the default implementation of the KlinkAdapter contract with a 
     * fake implementation that doesn't invoke the remote service
     *
     * @param Callable $callback Mock configuration callback. The callback will receive the Mockery\MockInterface object to be configured. Default null
     */
    public function withKlinkAdapterMock($callback = null)
    {
        $mockClass = Mockery::mock('Klink\DmsAdapter\Contracts\KlinkAdapter');

        if(!is_null($callback))
        {
            $mock = $callback($mockClass);

            // get the internal class, otherwise the Laravel service provider 
            // will break on type hinted methods
            if(method_exists($mock, 'getMock'))
            {
                $mock = $mock->getMock();
            }
        }
        else 
        {
            $mock = $mockClass;
        }

        $this->swap('Klink\DmsAdapter\Contracts\KlinkAdapter', $mock);
        $this->swap('Klink\DmsAdapter\KlinkAdapter', $mock);
        $this->swap('klinkadapter', $mock);


        return $mock;
    }

    public function withKlinkAdapterFake()
    {

        $fake = new FakeKlinkAdapter();

        $this->swap('Klink\DmsAdapter\Contracts\KlinkAdapter', $fake);
        $this->swap('Klink\DmsAdapter\KlinkAdapter', $fake);
        $this->swap('klinkadapter', $fake);


        return $fake;
    }

}
