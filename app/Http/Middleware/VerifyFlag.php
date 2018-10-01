<?php

namespace KBox\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

/**
 * Check if a feature flag is enabled before letting the request pass
 */
class VerifyFlag
{
    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next, $flag)
    {
        if (flags($flag)) {
            return $next($request);
        }
            
        if ($request->wantsJson()) {
            return new JsonResponse(['error' => trans('errors.forbidden_exception')], 403);
        }
        return response()->make(view('errors.403', []), 200);
    }
}
