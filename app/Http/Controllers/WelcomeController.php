<?php

namespace KBox\Http\Controllers;

use KBox\User;
use KBox\Option;

/**
 * --------------------------------------------------------------------------
 *  Welcome Controller
 * --------------------------------------------------------------------------
 *
 *  This controller renders the main login page.
 *  Is configured to only allow guests.
 *
 */
class WelcomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index()
    {
        $params = ['classes' => 'frontpage'];

        if (config('dms.are_guest_public_search_enabled')) {
            $params['filter'] = network_name();
        }

        $welcome_string = trans('dashboard.welcome.hero_title');
        
        if (Option::areContactsConfigured()) {
            $organization = Option::option('contact.name', '');
            
            if ($organization) {
                $welcome_string = trans('dashboard.welcome.hero_title_with_organization', compact('organization'));
            }
        }

        $params['welcome_string'] = $welcome_string;

        return view('welcome', $params);
    }
}
