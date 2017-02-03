<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\UserRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;
use Klink\DmsAdapter\Contracts\KlinkAdapter;

/**
 * Controller
 */
class NetworkAdministrationController extends Controller {

  private $adapter = null;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(KlinkAdapter $adapter) {

    $this->middleware('auth');

    $this->middleware('capabilities');

    $this->adapter = $adapter;

  }

  public function getIndex()
  {

    $osName = strtoupper(PHP_OS);

    $cards = \Cache::get('dms_network_cards', function(){

      return $this->getNetworkCards();
      
    });


    $ipRes = null;
    
    // $cards = $this->getNetworkCards();

    if(!is_array($cards)){
      $ipRes = $cards;
      $cards = array();
    }



    $res = $this->adapter->test();
// var_dump($res);
    $klink_test_message = $res['result'] === true ? 'success' : 'failed';

    return view('administration.network', ['pagetitle' => trans('administration.menu.network'), 'network_config' => $ipRes, 'network_cards' => $cards, 'klink_network_connection' => $klink_test_message, 'klink_network_connection_bool' => $res['result'], 'klink_network_connection_error' => $res['error']]);
  }




  private function getNetworkCards(){

      $osName = strtoupper(PHP_OS);

      if($osName === 'WINNT'){
        $ipRes = shell_exec('ipconfig');

        return $this->parseWindowsNetworkInfo($ipRes);
      }
      else if($osName === 'LINUX'){
        $ipRes = shell_exec('ifconfig');

        return $this->parseLinuxNetworkInfo($ipRes);

      }
      else if($osName === 'DARWIN'){
        $ipRes = shell_exec('ifconfig');

        // dd($ipRes);

        return $ipRes; //$this->parseMacNetworkInfo($ipRes);
      }
      
      return array();
      

  }


  private function parseLinuxNetworkInfo($input){

      $regexp = "^(?<interface>(?:[^\s]+)).*?(?:encap:(?<type>(?:\w+\.?))|$).*?(?:inet addr:(?<ip_address>(?:\d+\.?){4})|$)";

      $adapters = array_filter(preg_split("/\n\n/s", $input, null));
        $vals = array();
        foreach ($adapters as $int){
            preg_match("/".$regexp."/s", $int, $output);

            if(starts_with($output['interface'], 'wlan')){
              $output['type'] = 'Wireless';
            }

            $vals[] = $output;
        }
        return $vals;

  }

  private function parseWindowsNetworkInfo($input){

      $adapters = array_filter(preg_split("/\n\n/s", trim($input), null));

      unset($adapters[0]);

      $adapters = array_values($adapters);

      $vals = array();
      $count = count($adapters);

      if($count%2!==0){
        return array();
      }


      $ip_address = null;

      $ip_regexp = "^(.*)(IPv4).*:\s([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})*$";

      for ($i=0; $i < $count-1; $i=$i+2) { 
          
          $int = trim($adapters[$i]);
          $det = preg_split("/\n/s", trim($adapters[$i+1]), null);

          $type = str_contains($int, 'wireless') ? 'Wireless' : 'Ethernet';

          $name = trim(str_replace('Scheda', '', str_replace(':', '', $int)));

          $found = false;
          foreach ($det as $det_i) {
            preg_match("/".$ip_regexp."/s", $det_i, $output);

            if(!empty($output) && isset($output[2]) && isset($output[3]) && !$found){
              $ip_address = $output[3];
              $found = true;
            }
            
          }

          $vals[] = array('interface' => $name, 'type' => $type, 'ip_address' => $ip_address);

          $ip_address = null;
      }

      return $vals;

  }

}
