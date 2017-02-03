<?php namespace Klink\DmsAdapter\Traits;

/**
 * Add a swap method to hotswap an underlying instance of a contract/class
 */
trait SwapInstance
{
    /**
     * Hotswap the underlying instance of a contract/class.
     *
     * This can be used to inject a mock as the implementation of a contract/service
     *
     * <code>
     * $this->swap(
     *     'Klink\DmsAdapter\Contracts\KlinkAdapter', 
     *     Mockery::mock(Klink\DmsAdapter\Contracts\KlinkAdapter::class)
     * );
     * </code>
     *
     * @uses Illuminate\Contracts\Container::instance
     *
     * @param  string  $contract The contract/service that the $instance replaces
     * @param  mixed  $instance The new instance to be added
     * @return void
     */
    public function swap($contract, $instance)
    {
        $this->app->instance(
            $contract, 
            $instance
        );
    }
}
