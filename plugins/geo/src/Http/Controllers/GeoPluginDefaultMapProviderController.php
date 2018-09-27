<?php

namespace KBox\Geo\Http\Controllers;

use Exception;
use KBox\Geo\GeoService;
use Illuminate\Http\Request;
use KBox\Plugins\PluginManager;
use KBox\Http\Controllers\Controller;
use KBox\Geo\Http\Requests\ChangeDefaultMapProviderRequest;

/**
 * The controller that can change the default map provider
 */
class GeoPluginDefaultMapProviderController extends Controller
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
     * Update the default provider configuration.
     *
     * @param  \KBox\Geo\Http\Requests\ChangeDefaultMapProviderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ChangeDefaultMapProviderRequest $request)
    {
        $mapSettings = $this->service->config('map');
        
        $currentProviderId = $mapSettings['default'] ?? null;
        
        $newProviderId = $request->input('default');
        
        $newProviderLabel = $mapSettings['providers'][$newProviderId]['label'] ?? null;

        $mapSettings['default'] = $newProviderId;

        $this->service->config(['map' => $mapSettings]); // save the new configuration
        
        return redirect()->route('plugins.k-box-kbox-plugin-geo.mapproviders')->with([
            'flash_message' => trans('geo::settings.providers.default_provider_updated', ['name' => $newProviderLabel])
        ]);
    }
}
