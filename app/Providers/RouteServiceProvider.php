<?php

namespace KBox\Providers;

use KBox\Invite;
use Illuminate\Http\Request;
use OneOffTech\TusUpload\Tus;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     * @deprecated Use PHP callable syntax for route definitions
     */
    protected $namespace = 'KBox\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/search';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        $this->registerUuidModelBinding();

        Tus::routes();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Uuid model binding.
     *
     * Make explicit the use of the uuid field
     * for model binding as required when
     * efficient uuid storage is used.
     * See https://github.com/michaeldyrynda/laravel-model-uuid#route-model-binding
     *
     * @return void
     */
    private function registerUuidModelBinding()
    {
        Route::bind('invite', function ($invite) {
            try {
                return Invite::whereUuid($invite)->firstOrFail();
            } catch (InvalidUuidStringException $ex) {
                abort(404);
            }
        });
    }
}
