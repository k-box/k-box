<?php

namespace KBox\Http\Middleware;

use Closure;
use KBox\HomeRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $auth_user = Auth::guard($guard)->user();

                $intended = session()->get('url.intended', null);
                $dms_intended = session()->get('url.dms.intended', null);

                if (is_null($intended) && ! is_null($dms_intended)) {
                    session()->put('url.intended', $dms_intended);
                }

                return redirect()->intended(HomeRoute::get($auth_user));
            }
        }

        return $next($request);
    }
}
