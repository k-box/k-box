<?php

namespace KlinkDMS\Http\Middleware;

use Closure;

class TrustedProxyMiddleware
{
    /**
     * Add local addresses as trusted proxies to the request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->setTrustedProxies(['*', '127.0.0.1', '172.17.2.136', '172.17.0.0/16']);

        return $next($request);
    }
}
