<?php namespace KlinkDMS\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		if (Auth::guard($guard)->check())
		{
			$auth_user = Auth::guard($guard)->user();

			$intended = session()->get('url.intended', null);
			$dms_intended = session()->get('url.dms.intended', null);

			if(is_null($intended) && !is_null($dms_intended)){
				session()->put('url.intended', $dms_intended);
			}

			return redirect()->intended($auth_user->homeRoute());
		}

		return $next($request);
	}

}
