<?php

namespace KBox\Http\Middleware;

use Jenssegers\Agent\Agent;
use Closure;

/**
 * Blocks the unsupported browsers (i.e. IE 8) to reach any page
 */
class BlockUnsupportedBrowsers
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
        $agent = new Agent([], $request->userAgent());

        $browser = $agent->browser();
        $version = $agent->version($agent->browser());

        if ($browser === 'IE' && intval($version) <= 8) {
            return response(view('errors.unsupported-browser'));
        }

        return $next($request);
    }
}
