<?php

namespace KBox\Geo\Http\Controllers;

use Exception;
use KBox\Geo\GeoService;
use Illuminate\Http\Request;
use KBox\Plugins\PluginManager;
use KBox\Http\Controllers\Controller;
use KBox\Geo\Http\Requests\GeoServerSettingsRequest;

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

        return view('geo::maps', array_merge([
                'plugintitle' => trans('geo::settings.page_title'),
                'pagetitle' => trans('geo::settings.providers.title'),
            ], $currentSettings));
    }

}
