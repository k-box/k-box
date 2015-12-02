<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\Http\Requests;
use KlinkDMS\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use KlinkDMS\User;
use KlinkDMS\Http\Requests\ProfileUpdateRequest;
use KlinkDMS\Http\Requests\UserOptionUpdateRequest;

class UserProfileController extends Controller {


	/**
	   * Create a new controller instance.
	   *
	   * @return void
	   */
	  public function __construct() {

	      $this->middleware('auth');

//	      $this->middleware('capabilities');

	  }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Auth $auth)
	{

		$user = $auth->user();

		// $user = User::findOrFail($user_id);

		$stars_count = $user->starred()->count();
		$shares_count = $user->shares()->count();
		$documents_count = $user->documents()->count();
		$collections_count = $user->groups()->count();


		$pagetitle = trans('profile.page_title', ['name' => $user->name]);
		
		$language = $user->optionLanguage('en');

		

		return view('profile.user', compact('pagetitle', 'user', 'stars_count', 'shares_count', 'documents_count', 'collections_count', 'language'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Auth $auth, ProfileUpdateRequest $request)
	{
		$user = $auth->user();

		if($request->get('_change') === 'mail'){

			$user->email = $request->get('email');
			$message = trans('profile.messages.mail_changed');

		}
		else if($request->get('_change') === 'pass'){
			
			$user->password = \Hash::make($request->get('password'));
			$message = trans('profile.messages.password_changed');

		}
		else if($request->get('_change') === 'info'){

			$user->name = $request->get('name');
			$message = trans('profile.messages.name_changed');

		}
		else if($request->get('_change') === 'language'){

			$user->setOption(User::OPTION_LANGUAGE, $request->get(User::OPTION_LANGUAGE));
			
			$message = trans('profile.messages.language_changed');

		}

		$user->save();

		return redirect()->route('profile.index')->with([
            'flash_message' => $message
        ]);
	}


	/**
	 * used for Update user options (like the type of view of the lists)
	 */
	public function update(Auth $auth, UserOptionUpdateRequest $request)
	{
		$user = $auth->user();

		\Log::info('Updating user options', ['params' =>$request->all()]);

		if($request->has(User::OPTION_LIST_TYPE)){

			$user->setOption(User::OPTION_LIST_TYPE, $request->get(User::OPTION_LIST_TYPE));

			$user->save();

		}
		
		if($request->has(User::OPTION_LANGUAGE)){

			$user->setOption(User::OPTION_LANGUAGE, $request->get(User::OPTION_LANGUAGE));

			$user->save();

		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = User::findOrFail($id);

		$stars_count = $user->starred()->count();
		$shares_count = $user->shares()->count();
		$documents_count = $user->documents()->count();
		$collections_count = $user->groups()->count();


		$pagetitle = trans('profile.page_title', ['name' => $user->name]);

		

		return view('profile.user', compact('pagetitle', 'user', 'stars_count', 'shares_count', 'documents_count', 'collections_count'));
	}

}
