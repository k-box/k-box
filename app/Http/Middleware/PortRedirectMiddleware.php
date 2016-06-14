<?php

namespace KlinkDMS\Http\Middleware;

use Closure;
use Cache;
use Log;
use Exception;

/**
 * Change the request port to be the same as the configuration app.url 
 * so we can have apache listening somewhere and nginx routing using a different port
 * This is a workaround to make Carec setup works on port 999 without customize deeply 
 * the deployment
 * TODO: remove this when the setup will use PHP-FPM without Apache on the front
 */
class PortRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try{

            $host = Cache::get('app-overrided-host', function() {

                $full = config('app.url');
                $h = parse_url($full, PHP_URL_HOST);
                $p = parse_url($full, PHP_URL_PORT);

                return $h . (!is_null($p) ? ':' . $p : '');
            });
            
            $request->headers->set('host', $host);

        }catch(Exception $ex){
            Log::error('Host not overrided', ['ex' => $ex]);
        }

        return $next($request);
    }
}
