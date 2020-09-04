<?php

namespace KBox\Http\Controllers\Identities\Auth;

use KBox\HomeRoute;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Oneofftech\Identities\Auth\AuthenticatesUsersWithIdentity;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login via Identity Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users via their connected
    | identities provided by third party authentication services.
    | The controller uses a trait to conveniently provide its
    | functionality to your applications.
    |
    */

    use AuthenticatesUsersWithIdentity;

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
        $this->middleware('guest');
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

        return redirect()->intended(HomeRoute::get($user));
    }

    /**
     * Decide to which url redirect the user after login
     * @deprecated Seems to not be called anymore
     */
    public function redirectPath()
    {
        $user = $this->auth->user();
        
        return HomeRoute::get($user);
    }
}
