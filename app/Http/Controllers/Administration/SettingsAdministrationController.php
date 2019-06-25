<?php

namespace KBox\Http\Controllers\Administration;

use KBox\Http\Controllers\Controller;
use KBox\Http\Requests\SettingsSaveRequest;
use KBox\Option;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Auth\Guard as AuthGuard;

/**
 * Controller
 */
class SettingsAdministrationController extends Controller
{
    use DispatchesJobs;

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
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }
  
    public function index(AuthGuard $auth)
    {
        $data = [
        'pagetitle' => trans('administration.menu.settings'),
        Option::PUBLIC_CORE_ENABLED => ! ! Option::option(Option::PUBLIC_CORE_ENABLED, false),
        Option::PUBLIC_CORE_DEBUG => ! ! Option::option(Option::PUBLIC_CORE_DEBUG, false),
        Option::PUBLIC_CORE_URL => Option::option(Option::PUBLIC_CORE_URL, ''),
        Option::PUBLIC_CORE_USERNAME => Option::option(Option::PUBLIC_CORE_USERNAME, ''),
        Option::PUBLIC_CORE_PASSWORD => @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD, '')),
        Option::PUBLIC_CORE_NETWORK_NAME_EN => Option::option(Option::PUBLIC_CORE_NETWORK_NAME_EN, ''),
        Option::PUBLIC_CORE_NETWORK_NAME_RU => Option::option(Option::PUBLIC_CORE_NETWORK_NAME_RU, ''),
        Option::STREAMING_SERVICE_URL => Option::option(Option::STREAMING_SERVICE_URL, ''),
        ];

        return view('administration.settings.index', $data);
    }
  
    public function store(AuthGuard $auth, SettingsSaveRequest $request)
    {
        try {

            if ($request->input('public-settings-save-btn', false) !== false) {
                if ($request->has(Option::PUBLIC_CORE_URL) &&
                    $request->input(Option::PUBLIC_CORE_PASSWORD)) {
                    $url = $request->input(Option::PUBLIC_CORE_URL, null);
                    $password = $request->input(Option::PUBLIC_CORE_PASSWORD, null);
                    
                    $test_result = app('klinkadapter')->test($url, $password);
                    
                    if ($test_result['status'] === 'error') {
                        // failure
                        
                        $ex_message = $test_result['error'];
                    
                        return redirect()->back()->withInput()->withErrors([
                            'error' => trans('administration.settings.save_error', ['error' => $ex_message])
                        ]);
                    }
                    
                    Option::put(Option::PUBLIC_CORE_URL, $url);
                    Option::put(Option::PUBLIC_CORE_PASSWORD, base64_encode($password));
                    Option::put(Option::PUBLIC_CORE_CORRECT_CONFIG, true);
                    
                    \Log::info('Changed Network configuration', [
                        'by_user' => $auth->user()->id,
                        'new_config' => ['url' => $url]
                        ]);
                }

                Option::put(Option::PUBLIC_CORE_NETWORK_NAME_EN, $request->input(Option::PUBLIC_CORE_NETWORK_NAME_EN, ''));
                Option::put(Option::PUBLIC_CORE_NETWORK_NAME_RU, $request->input(Option::PUBLIC_CORE_NETWORK_NAME_RU, ''));

                \Cache::forget('network-name-en');
                \Cache::forget('network-name-ru');

                if ($request->has(Option::PUBLIC_CORE_ENABLED)) {
                    // if !active => activate it
                    Option::put(Option::PUBLIC_CORE_ENABLED, true);
                } else {
                    // disable it
                    Option::put(Option::PUBLIC_CORE_ENABLED, false);
                }
            }

            if ($request->has(Option::STREAMING_SERVICE_URL) && ! empty($request->input(Option::STREAMING_SERVICE_URL, null))) {
                Option::put(Option::STREAMING_SERVICE_URL, $request->input(Option::STREAMING_SERVICE_URL, null));
            } else {
                // disable it
                Option::put(Option::STREAMING_SERVICE_URL, '');
            }
      
            return redirect()->route('administration.settings.index')->with([
              'flash_message' => trans('administration.settings.saved')
            ]);
        } catch (\Exception $ex) {
            \Log::error('Settings saving error', ['error' => $ex, 'request' => $request->all()]);
        
            return redirect()->back()->withInput()->withErrors([
              'error' => trans('administration.settings.save_error', ['error' => $ex->getMessage()])
            ]);
        }
    }
}
