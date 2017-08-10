<?php

namespace KlinkDMS\Http\Controllers;

/**
 * Handle the redirect from the /dms routes to the root
 */
class DmsRoutesController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | DMS routes Controller
    |--------------------------------------------------------------------------
    |
    | Handle the redirects for the old /dms routes
    |
    */

    public function index()
    {
        return redirect()->route('frontpage');
    }

    public function show($route)
    {
        return redirect($route);
    }
}
