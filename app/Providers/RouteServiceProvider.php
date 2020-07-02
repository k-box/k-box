<?php

namespace KBox\Providers;

use OneOffTech\TusUpload\Tus;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use KBox\Invite;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
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
        parent::boot();

        $this->registerUuidModelBinding();

        Tus::routes();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
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
                return Invite::whereUuid($invite)->first() ?? abort(404);
            } catch (InvalidUuidStringException $ex) {
                abort(404);
            }
        });
    }
}
