<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\SettingsSaveRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;
use KlinkDMS\Option;
use Klink\DmsDocuments\DocumentsService;
use KlinkDMS\Commands\ReindexAll;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Contracts\Auth\Guard as AuthGuard;

/**
 * Controller
 */
class SettingsAdministrationController extends Controller {

  use DispatchesCommands;

  /*
  |--------------------------------------------------------------------------
  | Storage Management Page Controller
  |--------------------------------------------------------------------------
  |
  | This controller respond to actions for the "storage administration page".
  |
  */


  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {

    $this->middleware('auth');

    $this->middleware('capabilities');

  }
  
  
  public function index(AuthGuard $auth) {
      
    $data = array(
      'pagetitle' => trans('administration.menu.settings'),
      'map_visualization' => Option::is_map_visualization_enabled(),
      Option::PUBLIC_CORE_ENABLED => !!Option::option(Option::PUBLIC_CORE_ENABLED, false),
      Option::PUBLIC_CORE_DEBUG => !!Option::option(Option::PUBLIC_CORE_DEBUG, false),
      Option::PUBLIC_CORE_URL => Option::option(Option::PUBLIC_CORE_URL, ''),
      Option::PUBLIC_CORE_USERNAME => Option::option(Option::PUBLIC_CORE_USERNAME, ''),
      Option::PUBLIC_CORE_PASSWORD => @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD, '')),
      Option::PUBLIC_CORE_NETWORK_NAME_EN => Option::option(Option::PUBLIC_CORE_NETWORK_NAME_EN, ''),
      Option::PUBLIC_CORE_NETWORK_NAME_RU => Option::option(Option::PUBLIC_CORE_NETWORK_NAME_RU, ''),
      Option::SUPPORT_TOKEN => support_token(),
      Option::ANALYTICS_TOKEN => Option::analytics_token(),
    );

    return view('administration.settings.index', $data);
  }
  
  
  public function store(AuthGuard $auth, SettingsSaveRequest $request)
  {
    
      try{
          
            if($request->input('map-settings-save-btn', false) === ''){
          
                if($request->has(Option::MAP_VISUALIZATION_SETTING)){
                    // if !active => activate it
                    Option::put(Option::MAP_VISUALIZATION_SETTING, true);
                }
                else {
                    // disable it
                    Option::put(Option::MAP_VISUALIZATION_SETTING, false);
                }
          
            }
          
            if($request->input('support-settings-save-btn', false) === ''){
          
                if($request->has(Option::SUPPORT_TOKEN) && !empty($request->input(Option::SUPPORT_TOKEN, null))){
                    Option::put(Option::SUPPORT_TOKEN, $request->input(Option::SUPPORT_TOKEN, null));
                }
                else {
                    // disable it
                    Option::put(Option::SUPPORT_TOKEN, '');
                }
            }

            if($request->input('analytics-settings-save-btn', false) === ''){
          
                if($request->has(Option::ANALYTICS_TOKEN) && !empty($request->input(Option::ANALYTICS_TOKEN, null))){
                    Option::put(Option::ANALYTICS_TOKEN, $request->input(Option::ANALYTICS_TOKEN, null));
                }
                else {
                    // disable it
                    Option::put(Option::ANALYTICS_TOKEN, '');
                }
            }

            if($request->input('public-settings-save-btn', false) === ''){

                if($request->has(Option::PUBLIC_CORE_URL) &&
                    $request->input(Option::PUBLIC_CORE_USERNAME) &&
                    $request->input(Option::PUBLIC_CORE_PASSWORD)){
                        
                    $url = $request->input(Option::PUBLIC_CORE_URL, null);
                    $username = $request->input(Option::PUBLIC_CORE_USERNAME, null);
                    $password = $request->input(Option::PUBLIC_CORE_PASSWORD, null);
                    
                    $test_result = app('klinkadapter')->test(new \KlinkAuthentication($url, $username, $password, \KlinkVisibilityType::KLINK_PUBLIC));
                    
                    if(!$test_result['result']){
                        // failure
                        
                        $ex_message = $test_result['error']->getMessage();
                        
                        if(!is_null($test_result['error']->getPrevious())){
                            $ex_message .= ' ' . $test_result['error']->getPrevious()->getMessage();
                        }
                    
                        return redirect()->back()->withInput()->withErrors([
                            'error' => trans('administration.settings.save_error', ['error' => $ex_message])
                        ]);
                        
                    }
                    
                    Option::put(Option::PUBLIC_CORE_URL, $url);
                    Option::put(Option::PUBLIC_CORE_USERNAME, $username);
                    Option::put(Option::PUBLIC_CORE_PASSWORD, base64_encode($password));   
                    Option::put(Option::PUBLIC_CORE_CORRECT_CONFIG, true);
                    
                    \Log::info('Changed Network configuration', array(
                        'by_user' => $auth->user()->id,
                        'new_config' => array('url' => $url, 'username' => $username)
                        ));   
                        
                }

                Option::put(Option::PUBLIC_CORE_NETWORK_NAME_EN, $request->input(Option::PUBLIC_CORE_NETWORK_NAME_EN, ''));
                Option::put(Option::PUBLIC_CORE_NETWORK_NAME_RU, $request->input(Option::PUBLIC_CORE_NETWORK_NAME_RU, ''));

                \Cache::forget('network-name-en');
                \Cache::forget('network-name-ru');

            
                if($request->has(Option::PUBLIC_CORE_ENABLED)){
                    // if !active => activate it
                    Option::put(Option::PUBLIC_CORE_ENABLED, true);

                }
                else {
                    // disable it
                    Option::put(Option::PUBLIC_CORE_ENABLED, false);
                }
                
                if($request->has(Option::PUBLIC_CORE_DEBUG)){
                    Option::put(Option::PUBLIC_CORE_DEBUG, true);
                }
                else {
                    Option::put(Option::PUBLIC_CORE_DEBUG, false);
                }
                
           }
      
          return redirect()->route('administration.settings.index')->with([
              'flash_message' => trans('administration.settings.saved')
          ]);
      
      }catch(\Exception $ex){
        
        \Log::error('Settings saving error', ['error' => $ex, 'request' => $request->all()]);
        
        return redirect()->back()->withInput()->withErrors([
              'error' => trans('administration.settings.save_error', ['error' => $ex->getMessage()])
          ]);
      }
  }


}
