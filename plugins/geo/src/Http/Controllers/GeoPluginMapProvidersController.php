<?php

namespace KBox\Geo\Http\Controllers;

use Exception;
use KBox\Geo\GeoService;
use Illuminate\Http\Request;
use KBox\Plugins\PluginManager;
use KBox\Http\Controllers\Controller;
use KBox\Geo\Http\Requests\NewMapProviderRequest;
use KBox\Geo\Http\Requests\UpdateMapProviderRequest;

/**
 * The page that sets the map providers configuration
 */
class GeoPluginMapProvidersController extends Controller
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
        $currentSettings = $this->service->config('map');

        $defaults = $this->service->defaultConfig('map');

        return view('geo::maps', array_merge([
                'plugintitle' => trans('geo::settings.page_title'),
                'pagetitle' => trans('geo::settings.providers.title'),
            ], $currentSettings));
    }




    /**
     * Return the view for the creation of a new map provider
     */
    public function create()
    {
        return view('geo::providers.create', [
            'plugintitle' => trans('geo::settings.page_title'),
            'pagetitle' => trans('geo::settings.providers.create_title'),
        ]);
    }


    /**
     * Return the view for the edit of a map provider
     */
    public function edit($id)
    {
        $currentSettings = $this->service->config('map');

        $provider = $currentSettings['providers'][$id];

        return view('geo::providers.edit', [
            'plugintitle' => trans('geo::settings.page_title'),
            'pagetitle' => trans('geo::settings.providers.edit_title', ['name' => $provider['label']]),
            'providerId' => $id,
            'provider' => $provider
        ]);
    }

    
    /**
     * Create a new map provider and adds it to the configuration.
     *
     * @param  \KBox\Geo\Http\Requests\NewMapProviderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewMapProviderRequest $request)
    {
        $parameters = $request->only([
            'label',
            'url',
            'type',
            'maxZoom',
            'layers',
            'subdomains',
            'attribution',
        ]);

        $mapSettings = $this->service->config('map');
        $providersSetting = $mapSettings['providers'];

        // inject the new provider into the array
        $providersSetting[str_slug($parameters['label'])] = $parameters;
        $mapSettings['providers'] = $providersSetting;

        $this->service->config(['map' => $mapSettings]); // save the new configuration
        
        return redirect()->route('plugins.k-box-kbox-plugin-geo.mapproviders')->with([
            'flash_message' => trans('geo::settings.providers.provider_created', ['name' => $request->input('label', '')])
        ]);
    }
    
    /**
     * Update an existing provider configuration.
     *
     * @param  \KBox\Geo\Http\Requests\UpdateMapProviderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateMapProviderRequest $request)
    {
        $parameters = $request->only([
            'label',
            'url',
            'maxZoom',
            'layers',
            'subdomains',
            'attribution',
        ]);

        $mapSettings = $this->service->config('map');
        $providerSettings = $mapSettings['providers'][$id] ?? null;

        if(is_null($providerSettings)){
            return redirect()->back()
                        ->withInput()
                        ->withErrors(['label' => trans('geo::settings.validation.id.not_found')]);
        }
        $type = $request->input('type', null);
        
        if(!is_null($type) && $type !== $providerSettings['type']){
            return redirect()->back()
                        ->withInput()
                        ->withErrors(['type' => trans('geo::settings.validation.type.not_changeable', ['current' => $providerSettings['type'], 'new' => $type])]);
        }

        // inject the new provider into the array
        $mapSettings['providers'][$id] = array_merge($providerSettings, $parameters);

        $this->service->config(['map' => $mapSettings]); // save the new configuration
        
        return redirect()->route('plugins.k-box-kbox-plugin-geo.mapproviders')->with([
            'flash_message' => trans('geo::settings.providers.provider_updated', ['name' => $request->input('label', '')])
        ]);
    }
}
