<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\User;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

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

		if(\Config::get('dms.are_guest_public_search_enabled')){
			$params['filter'] = network_name();
		}

		return view('welcome', $params);

	}

}
