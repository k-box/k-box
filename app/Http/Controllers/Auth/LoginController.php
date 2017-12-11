<?php

namespace KBox\Http\Controllers\Auth;

use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/search';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Decide to which url redirect the user after login
     * @deprecated Seems to not be called anymore
     */
    public function redirectPath()
    {
        $user = $this->auth->user();
        
        return $user->homeRoute();
    }

    /**
     * Manage user redirection after has been authenticated
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    protected function authenticated(Request $request, $user)
    {
        $intended = session()->get('url.intended', null);
        $dms_intended = session()->pull('url.dms.intended', null);

        if (is_null($intended) && ! is_null($dms_intended)) {
            session()->put('url.intended', $dms_intended);
        }

        return redirect()->intended($user->homeRoute());
    }
}
