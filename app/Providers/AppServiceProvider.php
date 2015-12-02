<?php namespace KlinkDMS\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		// This service provider is a great spot to register your various container
		// bindings with the application. As you can see, we are registering our
		// "Registrar" implementation here. You can add your own bindings too!

		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'KlinkDMS\Services\Registrar'
		);

		// Loading workbench service provider only if running in console (aka Artisan) and the environment is set to 'development'

		if ($this->app->runningInConsole() && $this->app->environment('development')) {

            $this->app->register('Illuminate\Workbench\WorkbenchServiceProvider');

        }
	}

}
