<?php

namespace KBox\Geo\Http\Controllers;

use Exception;
use KBox\Geo\GeoService;
use Illuminate\Http\Request;
use KBox\Plugins\PluginManager;
use KBox\Http\Controllers\Controller;
use KBox\Geo\Http\Requests\EnableDisableMapProviderRequest;

/**
 * The controller that can enable/disable a map provider
 */
class GeoPluginEnableDisableMapProviderController extends Controller
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
     * @param  \KBox\Geo\Http\Requests\EnableDisableMapProviderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(EnableDisableMapProviderRequest $request)
    {
        $mapSettings = $this->service->config('map');

        $providerSettings = $mapSettings['providers'] ?? [];
        
        $providersToChange = array_wrap($request->input('provider', []));
        $enableProvider = (bool)$request->input('enable');
        $changedProviders = 0;
        
        if($providersToChange[0] ?? false){
            $updatedProviderLabel = $mapSettings['providers'][$providersToChange[0]]['label'] ?? null;
        }

        foreach ($providersToChange as $providerId) {
            if($enableProvider){
                $providerSettings[$providerId]['enable'] = true;
            }
            else {
                $providerSettings[$providerId]['enable'] = false;
            }
            $changedProviders++;
        }

        $mapSettings['providers'] = $providerSettings;
        $this->service->config(['map' => $mapSettings]); // save the new configuration
        
        return redirect()->route('plugins.k-box-kbox-plugin-geo.mapproviders')->with([
            'flash_message' => trans_choice( $enableProvider ? 'geo::settings.providers.providers_enabled' : 'geo::settings.providers.providers_disabled', $changedProviders,
            ['name' => $updatedProviderLabel ?? '',
            'count' => $changedProviders-1])
        ]);
    }
}
