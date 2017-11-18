<?php

namespace KlinkDMS\Facades;

use Illuminate\Support\Facades\Facade;
use KlinkDMS\Support\Testing\Fakes\KlinkStreamingClientFake;
use Oneofftech\KlinkStreaming\Client as StreamingClient;

/**
 * @see \Oneofftech\KlinkStreaming\Client
 */
class KlinkStreaming extends Facade
{
    /**
     * Replace the given streaming client with a local testing streaming client.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new KlinkStreamingClientFake);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return StreamingClient::class;
    }
}
