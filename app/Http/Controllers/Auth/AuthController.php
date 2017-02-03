<?php namespace KlinkDMS\Http\Controllers\Auth;

use KlinkDMS\Http\Controllers\Controller;
use Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

/**
 * Registration & Login Controller
 *
 * This controller handles the registration of new users, as well as the
 * authentication of existing users.
 */
class AuthController extends Controller 
{

	/**
	 * Base redirect after login
	 */
	public $redirectPath = '/search';

	use AuthenticatesAndRegistersUsers 
	{
		
		// I want to be able to set different redirections
		redirectPath as _redirectPath;
	}
    
	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
        $this->auth = $auth;
		$this->middleware('guest', ['except' => 'getLogout']);
	}
	
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
	/**
	 * Decide to which url redirect the user after login
	 */
	public function redirectPath()
	{
		
		$user = $this->auth->user();
		
		return $user->homeRoute();

	}

}
