<?php

namespace Klink\DmsAdapter\Concerns;

use Exception;

/**
 * 
 */
trait HasConnections
{
    /**
     * Contains the currently configured K-Search clients
     *
     * @var array
     */
    protected $connections = null;

    /**
     * Select the a connection.
     * 
     * @param string $key The connection key
     * @return mixed the selected connection if exists
     * @throws Exception if the specified key don't refer to a valid connection
     */
    private function selectConnection($key)
    {
        if(isset($this->connections[$key])){
            return $this->connections[$key];
        }

        throw new \Exception("No connection configured for: {$key}");
    }

    /**
     * The available configured connections
     * 
     * @return array
     */
    public function availableConnections()
    {
        return array_keys($this->connections);
    }
}
