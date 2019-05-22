<?php

namespace KBox\Http\Middleware;

use Closure;
use KBox\Services\ReadonlyMode;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class CheckForReadonlyMode
{
    /**
     * The application implementation.
     *
     * @var \KBox\Services\ReadonlyMode
     */
    protected $readonlyModeService;

    /**
     * The URIs that should be accessible while readonly mode is enabled.
     *
     * Default URIs include: login, logout, password reset
     * and administration
     *
     * @var array
     */
    protected $except = [
        'login',
        'logout',
        'administration/*',
        'password/*',
    ];

    protected $blockedMethods = [
        'POST',
        'PUT',
        'DELETE',
    ];

    /**
     * Create a new middleware instance.
     *
     * @param  \KBox\Services\ReadonlyMode  $readonlyModeService
     * @return void
     */
    public function __construct(ReadonlyMode $readonlyModeService)
    {
        $this->readonlyModeService = $readonlyModeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        if ($this->readonlyModeService->isReadonlyActive()) {
            if (! in_array($request->method(), $this->blockedMethods)) {
                return $next($request);
            }

            $data = $this->readonlyModeService->getReadonlyConfiguration();

            if (isset($data['allowed']) && IpUtils::checkIp($request->ip(), (array) $data['allowed'])) {
                return $next($request);
            }

            if ($this->inExceptArray($request)) {
                return $next($request);
            }

            throw new MaintenanceModeException($data['time'], $data['retry'], $data['message']);
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should be accessible in maintenance mode.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
