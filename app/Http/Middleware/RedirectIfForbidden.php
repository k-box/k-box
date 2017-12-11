<?php

namespace KBox\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

/**
 * Check if the user has the right permission to browse
 * the route and if not shows a Forbidden page
 */
class RedirectIfForbidden
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            $route_config = $request->route()->getAction();

            $required_perm = isset($route_config['permission']) ? $route_config['permission'] : \Config::get('route-permissions.'.$route_config['as']);

            if (is_null($required_perm)) {
                \Log::info('Null permission info', ['context' => 'RedirectIfForbidden middleware', 'param' => $request]);
            }

            $has_cap = is_array($required_perm) && array_key_exists('all', $required_perm) ? $this->auth->user()->can_all_capabilities($required_perm['all']) : $this->auth->user()->can_capability($required_perm);

            if ($has_cap) {
                return $next($request);
            }
            
            \Log::warning('User trying to access a page without capability', [
                'context' => 'RedirectIfForbidden middleware',
                'route_config' => $route_config, 'required_perm' => $required_perm, 'has_cap' => $has_cap]);

            if ($request->wantsJson()) {
                return new JsonResponse(['error' => trans('errors.forbidden_exception')], 403);
            }

            return response()->make(view('errors.403', []), 200);
        }

        return response('Authentication required.', 401);
    }
}
