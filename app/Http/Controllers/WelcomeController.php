<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\User;

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

		if(\Config::get('dms.are_guest_public_search_enabled'))
		{
			$params['filter'] = network_name();
		}

		return view('welcome', $params);

	}

}
