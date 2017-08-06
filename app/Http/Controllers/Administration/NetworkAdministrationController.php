<?php

namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Http\Controllers\Controller;
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
      $res = $this->adapter->test();
      $klink_test_message = $res['result'] === true ? 'success' : 'failed';

      return view('administration.network', [
      'pagetitle' => trans('administration.menu.network'),
      'klink_network_connection' => $klink_test_message,
      'klink_network_connection_bool' => $res['result'],
      'klink_network_connection_error' => $res['error']]);
  }
}
