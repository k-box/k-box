<?php

namespace KBox\Geo\Http\Controllers;

use Exception;
use KBox\Geo\GeoService;
use Illuminate\Http\Request;
use KBox\Plugins\PluginManager;
use KBox\Http\Controllers\Controller;
use KBox\Geo\Http\Requests\GeoServerSettingsRequest;

/**
 * The page that sets the plugin configuration
 */
class GeoPluginSettingsController extends Controller
{
    private $service;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GeoService $service)
    {
        $this->middleware('auth');

        $this->service = $service;
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index()
    {
        $currentSettings = $this->service->config();

        $connected = false;
        $version = null;
        $error = null;
        if($this->service->isEnabled()){
            try{
                $connected = true;
                $version = $this->service->connection()->version();
            }catch(Exception $ex){
                $error = $ex->getMessage();
            }
        }

        return view('geo::settings', array_merge([
            'pagetitle' => trans('geo::settings.page_title'),
            'enabled' => $this->service->isEnabled(),
            'connected' => $connected,
            'version' => $version,
            'error' => $error,
        ], $currentSettings));
    }

    /**
     * Update the plugin settings.
     *
     * @param  \KBox\Geo\Http\Requests\GeoServerSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(GeoServerSettingsRequest $request)
    {
        $parameters = $request->only([
            'geoserver_url',
            'geoserver_username',
            'geoserver_password',
            'geoserver_workspace',
        ]);

        try{
            GeoService::testConnection($parameters);
        }catch(Exception $ex){
            // report the exception as a validation error for the geoserver_url field
            return redirect()->route('plugins.k-box-kbox-plugin-geo.settings')
                    ->withInput()
                    ->withErrors(['geoserver_url' => $ex->getMessage()]);
        }

        $this->service->config($parameters);
        
        return redirect()->route('plugins.k-box-kbox-plugin-geo.settings')->with([
            'flash_message' => trans('plugins.messages.settings_saved')
        ]);
    }

}
