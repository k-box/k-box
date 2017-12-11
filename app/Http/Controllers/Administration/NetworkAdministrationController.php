<?php

namespace KBox\Http\Controllers\Administration;

use KBox\Http\Controllers\Controller;
use Klink\DmsAdapter\Contracts\KlinkAdapter;

/**
 * Network page controller
 */
class NetworkAdministrationController extends Controller
{
    private $adapter = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(KlinkAdapter $adapter)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->adapter = $adapter;
    }

    /**
     * Get the connection status to the K-Core and show it on the view
     */
    public function getIndex()
    {
        $local = $this->adapter->canConnect('private');
      
      
        $info = [
          'local_connection' => $local['status'] === 'ok' ? 'success' : 'failed',
          'local_connection_bool' => $local['status'] === 'ok',
          'local_connection_error' => isset($local['error']) ? $local['error'] : ''
        ];
        
        if (network_enabled()) {
            $remote = $this->adapter->canConnect('public');

            $remote_info = [
                'remote_connection' => $remote['status'] === 'ok' ? 'success' : 'failed',
                'remote_connection_bool' => $remote['status'] === 'ok',
                'remote_connection_error' => isset($remote['error']) ? $remote['error'] : ''
            ];

            $info = array_merge($info, $remote_info);
        }

        return view('administration.network',
        array_merge(['pagetitle' => trans('administration.menu.network')], $info));
    }
}
