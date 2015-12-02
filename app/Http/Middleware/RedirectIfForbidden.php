<?php namespace KlinkDMS\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\Middleware;

/**
 * Check if the user has the right permission to browse 
 * the route and if not shows a Forbidden page
 */
class RedirectIfForbidden implements Middleware {

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

		if ($this->auth->check())
		{

			$route_config = $request->route()->getAction();

			$required_perm = isset($route_config['permission']) ? $route_config['permission'] : \Config::get('route-permissions.' . $route_config['as']);

			if(is_null($required_perm)){
				\Log::info('Null permission info', array('context' => 'RedirectIfForbidden middleware', 'param' => $request));
			}

			$has_cap = $this->auth->user()->can($required_perm);
			            
			if( $has_cap ){

				return $next($request);

			}
            
			\Log::warning('User trying to access a page without capability', array(
				'context' => 'RedirectIfForbidden middleware', 
				'route_config' => $route_config, 'required_perm' => $required_perm, 'has_cap' => $has_cap));

//			throw new \KlinKDMS\Exceptions\ForbiddenException();
			if($request->wantsJson()){
				return new JsonResponse(array('error' => trans('documents.messages.delete_forbidden')), 403);
			}

			return view('errors.403');
		}

		return response('Authentication required.', 401);
	}

}
